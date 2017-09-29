<?php
/**
 * 万能小店模块微站定义
 *
 * @author wannengjun
 * @url 
 */
defined('IN_IA') or exit('Access Denied');
include "model.php";
class Wn_storexModuleSite extends WeModuleSite {
	public function __call($name, $arguments) {
		$isWeb = stripos($name, 'doWeb') === 0;
		$isMobile = stripos($name, 'doMobile') === 0;
		if ($isWeb || $isMobile) {
			$dir = IA_ROOT . '/addons/' . $this->modulename . '/inc/';
			if ($isWeb) {
				$dir .= 'web/';
				$fun = strtolower(substr($name, 5));
				$func = IA_ROOT . '/addons/wn_storex/function/function.php';
				if (is_file($func)) {
					require $func;
				}
			}
			if ($isMobile) {
				$dir .= 'mobile/';
		 		$fun = strtolower(substr($name, 8));
		 		$init = $dir . '__init.php';
		 		$func = IA_ROOT . '/addons/wn_storex/function/function.php';
				if (is_file($init)) {
					require $init;
				}
				if (is_file($func)) {
					require $func;
				}
			}
 			$file = $dir . $fun . '.inc.php';
			if (file_exists($file)) {
				require $file;
				exit;
			} else {
				$dir = str_replace("addons", "framework/builtin", $dir);
				$file = $dir . $fun . '.inc.php';
				if (file_exists($file)) {
					require $file;
					exit;
				}
			}
		}
		trigger_error("访问的方法 {$name} 不存在.", E_USER_WARNING);
		return null;
	}

	public function doWebCoupon() {
		header("Location: {$this->createWebUrl('couponmanage')}");
		exit;
	}

	public function doWebExchange() {
		header("Location: {$this->createWebUrl('goodsexchange')}");
		exit;
	}
	
	public function doWebStat() {
		header("Location: {$this->createWebUrl('statcredit1')}");
		exit;
	}

	public function doMobileDisplay() {
		global $_W, $_GPC;
		load()->model('mc');
		mc_oauth_userinfo();
		include $this->template('home');
	}

	protected function pay($params = array(), $mine = array()) {
		global $_W;
		if (!$this->inMobile) {
			message(error(-1, '支付功能只能在手机上使用'), '', 'ajax');
		}
		$params['module'] = $this->module['name'];
		$pars = array();
		$pars[':uniacid'] = $_W['uniacid'];
		$pars[':module'] = $params['module'];
		$pars[':tid'] = $params['tid'];
		//如果价格为0 直接执行模块支付回调方法
		if ($params['fee'] <= 0) {
			$pars['from'] = 'return';
			$pars['result'] = 'success';
			$pars['type'] = '';
			$pars['tid'] = $params['tid'];
			$site = WeUtility::createModuleSite($pars[':module']);
			$method = 'payResult';
			if (method_exists($site, $method)) {
				exit($site->$method($pars));
			}
		}
		$pars[':openid'] = $_W['openid'];
		$sql = 'SELECT * FROM ' . tablename('core_paylog') . ' WHERE `uniacid`=:uniacid AND `module`=:module AND `tid`=:tid AND `openid`=:openid';
		$log = pdo_fetch($sql, $pars);
		if (empty($log)) {
			$log = array(
				'uniacid' => $_W['uniacid'],
				'acid' => $_W['acid'],
				'openid' => $_W['openid'],
				'module' => $this->module['name'],
				'tid' => $params['tid'],
				'fee' => $params['fee'],
				'card_fee' => $params['fee'],
				'status' => '0',
				'is_usecard' => '0',
			);
			pdo_insert('core_paylog', $log);
		}
		if ($log['status'] == '1') {
			message(error(-1, '这个订单已经支付成功, 不需要重复支付.'), '', 'ajax');
		}
		$payment = uni_setting(intval($_W['uniacid']), array('payment', 'creditbehaviors'));
		if (!is_array($payment['payment'])) {
			message(error(-1, '没有有效的支付方式, 请联系网站管理员.'), '', 'ajax');
		}
		$pay = $payment['payment'];
		if (empty($_W['member']['uid'])) {
			$pay['credit'] = false;
		}
		$pay['delivery']['switch'] = 0;
		foreach ($pay as $paytype => $val) {
			if (empty($val['switch'])) {
				unset($pay[$paytype]);
			} else {
				$pay[$paytype] = array();
				$pay[$paytype]['switch'] = $val['switch'];
			}
		}
		if (!empty($pay['credit'])) {
			$credtis = mc_credit_fetch($_W['member']['uid']);
		}
		$pay_data['pay'] = $pay;
		$pay_data['credits'] = $credtis;
		$pay_data['params'] = json_encode($params);
		return $pay_data;
	}

	public function payResult($params) {
		global $_GPC, $_W;
		load()->model('mc');
		mload()->model('card');
		if ($params['type'] == 'credit') {
			$paytype = 1;
		} elseif ($params['type'] == 'wechat') {
			$paytype = 21;
		} elseif ($params['type'] == 'alipay') {
			$paytype = 22;
		} elseif ($params['type'] == 'delivery') {
			$paytype = 3;
		}
		$recharge_info = pdo_get('mc_credits_recharge', array('uniacid' => $_W['uniacid'], 'tid' => $params['tid']), array('id', 'backtype', 'fee', 'openid', 'uid', 'type'));
		if (!empty($recharge_info)) {
			if ($params['result'] == 'success' && $params['from'] == 'notify') {
				$fee = $params['fee'];
				$total_fee = $fee;
				$data = array('status' => $params['result'] == 'success' ? 1 : -1);
				//如果是微信支付，需要记录transaction_id。
				if ($params['type'] == 'wechat') {
					$data['transid'] = $params['tag']['transaction_id'];
					$params['user'] = mc_openid2uid($params['user']);
				}
				pdo_update('mc_credits_recharge', $data, array('tid' => $params['tid']));
				$paydata = array('wechat' => '微信', 'alipay' => '支付宝', 'baifubao' => '百付宝', 'unionpay' => '银联');
				$card_setting = card_setting_info();
				//余额充值
				if (empty($recharge_info['type']) || $recharge_info['type'] == 'credit') {
					$setting = uni_setting($_W['uniacid'], array('creditbehaviors', 'recharge'));
					$credit = $setting['creditbehaviors']['currency'];
					$card_recharge = $card_setting['params']['cardRecharge'];
					$recharge_params = array();
					if ($card_recharge['params']['recharge_type'] == 1) {
						$recharge_params = $card_recharge['params'];
					}
					if (empty($credit)) {
						message('站点积分行为参数配置错误,请联系服务商', '', 'error');
					} else {
						if ($recharge_params['recharge_type'] == '1') {
							$recharges = $recharge_params['recharges'];
						}
						if ($recharge_info['backtype'] == '2') {
							$total_fee = $fee;
						} else {
							foreach ($recharges as $key => $recharge) {
								if ($recharge['backtype'] == $recharge_info['backtype'] && $recharge['condition'] == $recharge_info['fee']) {
									if ($recharge_info['backtype'] == '1') {
										$total_fee = $fee;
										$add_credit = $recharge['back'];
									} else {
										$total_fee = $fee + $recharge['back'];
									}
								}
							}
						}
						if ($recharge_info['backtype'] == '1') {
							$add_str = ",充值成功,返积分{$add_credit}分,本次操作共增加余额{$total_fee}元,积分{$add_credit}分";
							$remark = '用户通过' . $paydata[$params['type']] . '充值' . $fee . $add_str;
							$record[] = $params['user'];
							$record[] = $remark;
							mc_credit_update($params['user'], 'credit1', $add_credit, $record);
							mc_credit_update($params['user'], 'credit2', $total_fee, $record);
							mc_notice_recharge($recharge_info['openid'], $recharge_info['uid'], $total_fee, '', $remark);
						} else {
							$add_str = ",充值成功,本次操作共增加余额{$total_fee}元";
							$remark = '用户通过' . $paydata[$params['type']] . '充值' . $fee . $add_str;
							$record[] = $params['user'];
							$record[] = $remark;
							$record[] = $this->module['name'];
							mc_credit_update($params['user'], 'credit2', $total_fee, $record);
							mc_notice_recharge($recharge_info['openid'], $params['user'], $total_fee, '', $remark);
						}
					}
				} elseif ($recharge_info['type'] == 'card_nums') {
					$card_recharge = $card_setting['params']['cardNums'];
					if ($card_recharge['params']['nums_status'] == 1) {
						$recharges = $card_recharge['params']['nums'];
						foreach ($recharges as $key => $recharge) {
							if ($recharge['recharge'] == $recharge_info['fee']) {
								$total_fee = $fee;
								$nums = $recharge['num'];
								break;
							}
						}
						$add_str = ",充值成功,增加会员卡使用次数{$nums}";
						$remark = '用户通过' . $paydata[$params['type']] . '充值' . $fee . $add_str;
						$record[] = $params['user'];
						$record[] = $remark;
						$record[] = $this->module['name'];
						$card_info = pdo_get('storex_mc_card_members', array('openid' => $recharge_info['openid']), array('nums'));
						pdo_update('storex_mc_card_members', array('nums' => ($card_info['nums'] + $nums)), array('openid' => $recharge_info['openid']));
						mc_notice_recharge($recharge_info['openid'], $params['user'], $total_fee, '', $remark);
					}
				} elseif ($recharge_info['type'] == 'card_times') {
					$card_recharge = $card_setting['params']['cardTimes'];
					if ($card_recharge['params']['times_status'] == 1) {
						$recharges = $card_recharge['params']['times'];
						foreach ($recharges as $key => $recharge) {
							if ($recharge['recharge'] == $recharge_info['fee']) {
								$total_fee = $fee;
								$times = $recharge['time'];
								break;
							}
						}
						$add_str = ",充值成功,增加{$times}天会员时间";
						$remark = '用户通过' . $paydata[$params['type']] . '充值' . $fee . $add_str;
						$record[] = $params['user'];
						$record[] = $remark;
						$record[] = $this->module['name'];
						$card_info = pdo_get('storex_mc_card_members', array('openid' => $recharge_info['openid']), array('endtime'));
						if ($card_info['endtime'] < TIMESTAMP) {
							pdo_update('storex_mc_card_members', array('endtime' => (TIMESTAMP + $times * 86400)), array('openid' => $recharge_info['openid']));
						} else {
							pdo_update('storex_mc_card_members', array('endtime' => ($card_info['endtime'] + $times * 86400)), array('openid' => $recharge_info['openid']));
						}
						mc_notice_recharge($recharge_info['openid'], $params['user'], $total_fee, '', $remark);
					}
				}
			}
			$url = $this->createMobileurl('display', array('pay_type' => 'recharge'));
			//如果消息是用户直接返回（非通知），则提示一个付款成功
			if ($params['from'] == 'return') {
				if ($params['result'] == 'success') {
					message('支付成功！', $url, 'success');
				} else {
					message('支付失败！', $url, 'error');
				}
			}
		}
	}
	
}