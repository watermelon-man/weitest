<?php

class CommonValStr 
{
	//Common
	public static $COMMON_OPRATION_SUCCESS_VAL = 0;
	public static $COMMON_OPRATION_SUCCESS_STR = '操作成功!';

	//中奖状态
	public static $WINNER_STATUS_WIN_SEND_VAL = 2;
	public static $WINNER_STATUS_WIN_SEND_STR = '已中奖/已发放';
	public static $WINNER_STATUS_WIN_NOTSEND_VAL = 1;
	public static $WINNER_STATUS_WIN_NOTSEND_STR = '已中奖/未发放';
	public static $WINNER_STATUS_NOTWIN_VAL = 0;
	public static $WINNER_STATUS_NOTWIN_STR = '未中奖';

	//外链提示
	public static $OUTSIDE_LINK_REMIND_VAL = -1;
	public static $OUTSIDE_LINK_REMIND_STR = '请关注公众号后，进入公众号使用摇一摇！';

	//ShakeActivity 规则
	public static $REPLY_WORDS_EXIT_VAL = 1;
	public static $REPLY_WORDS_EXIT_STR = '回复关键字重复!';
	public static $ACTIVITY_SAVE_SUCCESS_VAL = 2;
	public static $ACTIVITY_SAVE_SUCCESS_STR = '活动信息保存成功!';
	public static $ACTIVITY_DEL_SUCCESS_VAL = 3;
	public static $ACTIVITY_DEL_SUCCESS_STR = '活动规则删除成功!';
	public static $ACTIVITY_EDIT_SUCCESS_VAL = 4;
	public static $ACTIVITY_EDIT_SUCCESS_STR = '活动规则编辑成功!';
	public static $ACTIVITY_ADD_SUCCESS_VAL = 5;
	public static $ACTIVITY_ADD_SUCCESS_STR = '活动规则添加成功!';
	public static $ACTIVITY_EDITLIST_SUCCESS_VAL = 6;
	public static $ACTIVITY_EDITLIST_SUCCESS_STR = '编辑后列表成功!';
	public static $ACTIVITY_COPY_SUCCESS_VAL = 6;
	public static $ACTIVITY_COPY_SUCCESS_STR = '活动复制成功!';
	public static $ACTIVITY_AWARDS_COPY_SUCCESS_VAL = 7;
	public static $ACTIVITY_AWARDS_COPY_SUCCESS_STR = '活动及所属奖品复制成功!';
	public static $ACTIVITY_DEL_ALL_SUCCESS_VAL = 8;
	public static $ACTIVITY_DEL_ALL_SUCCESS_STR = '活动规则全部删除成功!';
	public static $ACTIVITY_DETAIL_SUCCESS_VAL = 9;
	public static $ACTIVITY_DETAIL_SUCCESS_STR = '活动规则详情成功!';
	public static $ACTIVITY_LIST_SUCCESS_VAL = 10;
	public static $ACTIVITY_LIST_SUCCESS_STR = '活动规则列表成功!';

	//ShakeAward 奖品
	public static $AWARD_NAME_EXIT_VAL = 11;
	public static $AWARD_NAME_EXIT_STR = '奖品名称重复!';
	public static $AWARD_EDIT_SUCCESS_VAL = 12;
	public static $AWARD_EDIT_SUCCESS_STR = '奖品编辑成功!';
	public static $AWARD_ADD_SUCCESS_VAL = 13;
	public static $AWARD_ADD_SUCCESS_STR = '奖品添加成功!';
	public static $AWARD_DEL_SUCCESS_VAL = 14;
	public static $AWARD_DEL_SUCCESS_STR = '奖品删除成功!';
	public static $AWARD_DEL_ALL_SUCCESS_VAL = 15;
	public static $AWARD_DEL_ALL_SUCCESS_STR = '奖品全部删除成功!';
	public static $AWARD_EDITLIST_SUCCESS_VAL = 16;
	public static $AWARD_EDITLIST_SUCCESS_STR = '奖品编辑后列表成功!';
	public static $AWARD_DETAIL_SUCCESS_VAL = 17;
	public static $AWARD_DETAIL_SUCCESS_STR = '奖品详情成功!';
	public static $AWARD_LIST_SUCCESS_VAL = 18;
	public static $AWARD_LIST_SUCCESS_STR = '奖品列表成功!';

	//ShakeWinner 获奖者
	public static $WINNER_NAME_EXIT_VAL = 19;
	public static $WINNER_NAME_EXIT_STR = '获奖者名称重复!';
	public static $WINNER_EDIT_SUCCESS_VAL = 20;
	public static $WINNER_EDIT_SUCCESS_STR = '获奖者编辑成功!';
	public static $WINNER_ADD_SUCCESS_VAL = 21;
	public static $WINNER_ADD_SUCCESS_STR = '获奖者添加成功!';
	public static $WINNER_DEL_SUCCESS_VAL = 22;
	public static $WINNER_DEL_SUCCESS_STR = '获奖者删除成功!';
	public static $WINNER_DEL_ALL_SUCCESS_VAL = 23;
	public static $WINNER_DEL_ALL_SUCCESS_STR = '获奖者全部删除成功!';
	public static $WINNER_EDITLIST_SUCCESS_VAL = 24;
	public static $WINNER_EDITLIST_SUCCESS_STR = '获奖者编辑后列表成功!';
	public static $WINNER_DETAIL_SUCCESS_VAL = 25;
	public static $WINNER_DETAIL_SUCCESS_STR = '获奖者详情成功!';
	public static $WINNER_LIST_SUCCESS_VAL = 26;
	public static $WINNER_LIST_SUCCESS_STR = '获奖者列表成功!';
	public static $WINNER_EDIT_STATUS_SUCCESS_VAL = 27;
	public static $WINNER_EDIT_STATUS_SUCCESS_STR = '获奖者状态编辑成功!';

	//活动条件检测
	public static $CHECK_ACTIVITY_NOT_CARE_VAL = 28;
	public static $CHECK_ACTIVITY_NOT_CARE_STR = '请先关注公众号！';
	public static $CHECK_ACTIVITY_ERR_REPLYWORDS_VAL = 29;
	public static $CHECK_ACTIVITY_ERR_REPLYWORDS_STR = '请回复正确的关键字!';
	public static $CHECK_ACTIVITY_NONE_ACTID_PARAMETER_VAL = 36;
	public static $CHECK_ACTIVITY_NONE_ACTID_PARAMETER_STR = '无活动id标识!';
	public static $CHECK_ACTIVITY_NOT_START_VAL = 30;
	public static $CHECK_ACTIVITY_NOT_START_STR = '活动还未开始!';
	public static $CHECK_ACTIVITY_ALREADY_END_VAL = 31;
	public static $CHECK_ACTIVITY_ALREADY_END_STR = '活动已经结束!';
	public static $CHECK_ACTIVITY_NOT_ONLINE_VAL = 32;
	public static $CHECK_ACTIVITY_NOT_ONLINE_STR = '活动已下线!';
	public static $CHECK_ACTIVITY_SUCCESS_VAL = 33;
	public static $CHECK_ACTIVITY_SUCCESS_STR = '活动检测通过!';
	public static $CHECK_ACTIVITY_NOT_USRINFO_VAL = 34;
	public static $CHECK_ACTIVITY_NOT_USRINFO_STR = '获取不到用户信息!';
	public static $CHECK_ACTIVITY_DAYTOTAL_REACH_VAL = 35;
	public static $CHECK_ACTIVITY_DAYTOTAL_REACH_STR = '用户每日获奖总数已达到上限!';
	public static $CHECK_ACTIVITY_DAYTOTAL_NONE_VAL = 37;
	public static $CHECK_ACTIVITY_DAYTOTAL_NONE_STR = '未配置用户每日获奖总数!';
	public static $CHECK_ACTIVITY_TOTAL_REACH_VAL = 38;
	public static $CHECK_ACTIVITY_TOTAL_REACH_STR = '用户可获奖总数已达到上限!';
	public static $CHECK_ACTIVITY_TOTAL_NONE_VAL = 39;
	public static $CHECK_ACTIVITY_TOTAL_NONE_STR = '未配置用户可获奖总数!';
	public static $CHECK_ACTIVITY_SINGLE_ACTIVITY_TOTAL_REACH_VAL = 40;
	public static $CHECK_ACTIVITY_SINGLE_ACTIVITY_TOTAL_REACH_STR = '奖品总数已达到上限!';
	public static $CHECK_ACTIVITY_SINGLE_ACTIVITY_TOTAL_NONE_VAL = 41;
	public static $CHECK_ACTIVITY_SINGLE_ACTIVITY_TOTAL_NONE_STR = '未配置本期奖品总数!';
	public static $CHECK_ACTIVITY_NOMORE_AWARDS_VAL = 42;
	public static $CHECK_ACTIVITY_NOMORE_AWARDS_STR = '奖品抢完了!';
	public static $CHECK_ACTIVITY_NOTWIN_REMINDER_VAL = 43;
	public static $CHECK_ACTIVITY_NOTWIN_REMINDER_STR = '未抽到奖品，继续努力摇吧！';
	public static $CHECK_ACTIVITY_WIN_REMINDER_VAL = 44;
	public static $CHECK_ACTIVITY_WIN_REMINDER_STR = '成功赢得奖品！';

	//获取用户信息
	public static $GET_USRINFO_FAIL_VAL = 45;
	public static $GET_USRINFO_FAIL_STR = '链接无效，请关注公众号后，在公众号中摇奖!';
	public static $GET_USRINFO_SUCCESS_VAL = 46;
	public static $GET_USRINFO_SUCCESS_STR = '获取用户信息成功!';

	//大屏幕
	public static $BIGSCREEN_LIST_START_ACTIVITYS_SUCCESS_VAL = 47;
	public static $BIGSCREEN_LIST_START_ACTIVITYS_SUCCESS_STR = '大屏幕活动列表成功!';
	public static $BIGSCREEN_START_STOP_ACTIVITY_FAIL_VAL = 48;
	public static $BIGSCREEN_START_STOP_ACTIVITY_FAIL_STR = 'ruleid规则标识，started标识不合法！';
	public static $BIGSCREEN_START_STOP_ACTIVITY_NOTSAME_VAL = 49;
	public static $BIGSCREEN_START_STOP_ACTIVITY_NOTSAME_STR = '活动已在其他页面启动/停止，将自动刷新页面已更新状态！';
	public static $BIGSCREEN_START_STOP_ACTIVITY_SUCCESS_VAL = 50;
	public static $BIGSCREEN_START_STOP_ACTIVITY_SUCCESS_STR = '大屏幕开始停止活动成功！';
	public static $BIGSCREEN_LAUNCH_ACTIVITY_SUCCESS_VAL = 51;
	public static $BIGSCREEN_LAUNCH_ACTIVITY_SUCCESS_STR = '大屏幕启动成功！';
	public static $BIGSCREEN_CHECK_SHAKING_FANS_SUCCESS_VAL = 52;
	public static $BIGSCREEN_CHECK_SHAKING_FANS_SUCCESS_STR = '检测摇动粉丝成功！';
	public static $BIGSCREEN_CHECK_WINNING_FANS_SUCCESS_VAL = 53;
	public static $BIGSCREEN_CHECK_WINNING_FANS_SUCCESS_STR = '检测获奖粉丝成功！';
	public static $BIGSCREEN_AWARD_MINTIME_LIMIT_SUCCESS_VAL = 54;
	public static $BIGSCREEN_AWARD_MINTIME_LIMIT_SUCCESS_STR = '最小获奖时间间隔限制，排队中，继续摇吧！';

	//大屏幕获奖列表

}