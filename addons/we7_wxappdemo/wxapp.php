<?php
/**
 * 微擎小程序模板模块小程序接口定义
 *
 * @author 微擎团队
 * @url 
 */
defined('IN_IA') or exit('Access Denied');

class We7_wxappdemoModuleWxapp extends WeModuleWxapp {
	public function doPageTest(){
		global $_GPC, $_W;
		$errno = 0;
		$message = '返回消息';
		$data = array();
		return $this->result($errno, $message, $data);
	}
	public function doPageList(){
		global $_GPC, $_W;
		$message = '成功';
		$data = array(array("name"=>"李丽", "sex"=>"1", "age"=>"31"), array("name"=>"王思", "sex"=>"2", "age"=>"19"), array("name"=>"三明", "sex"=>" ", "age"=>"19"));
		return $this->result(0, $message, $data);
	}

	public function doPagePay() {
		global $_GPC;
		//构造订单信息，此处订单随机生成，业务中应该把此订单入库，支付成功后，根据此订单号更新用户是否支付成功
		$order = array(
			'tid' => date('YmdHis'),
			'user' => $_W['openid'],
			'fee' => floatval($_GPC['sum']),
			'title' => '微擎小程序测试支付',
		);
		$pay_params = $this->pay($order);
		if (is_error($pay_params)) {
			return $this->result(1, '支付失败，请重试');
		}
		return $this->result(1, '', $pay_params);
	}

	public function payResult($pay_result) {
		if ($pay_result['result'] == 'success') {
			//此处会处理一些支付成功的业务代码

		}
		print_r($pay_result);
		return true;
	}

	public function doPageTodoLists(){
		global $_GPC, $_W;
		$todo_list = array();
		$todo_list = pdo_getall('wxappdemo_todo', array('openid' => $_SESSION['openid']), array('id', 'title', 'done', 'datetime'));
		return $this->result(0, '成功', $todo_list);
	}

	public function doPageTodoAdd(){
		global $_GPC, $_W;
		$todo_data = array(
			'title' => $_GPC['title'],
			'done' => 1,
			'openid' => $_SESSION['openid'],
			'datetime' => time()
		);
		$result = pdo_insert('wxappdemo_todo', $todo_data);
		if (!empty($result)) {
			$uid = pdo_insertid();
			return $this->result(0, '成功', $uid);
		}
	}

	public function doPageTodoEdit(){
		global $_GPC, $_W;
		$id = intval($_GPC['id']);
		$todo_data = array(
			'datetime' => time()
		);
		if ($_GPC['title']) {
			$todo_data['title'] = $_GPC['title'];
		}
		if (intval($_GPC['done'])) {
			$todo_data['done'] = intval($_GPC['done']);
		}
		$result = pdo_update('wxappdemo_todo', $todo_data, array('id' => $id));
		if (!empty($result)) {
			return $this->result(0, '成功', $todo_data);
		}
	}

	public function doPageTodoDel(){
		global $_GPC, $_W;
		$id = intval($_GPC['id']);
		$result = pdo_delete('wxappdemo_todo', array('id' => $id));
		if (!empty($result)) {
			return $this->result(0, '成功', '');
		}
	}
}