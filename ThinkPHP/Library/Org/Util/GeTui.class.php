<?php
namespace Org\Util;
Vendor('getui.IGt','','.Push.php');
class GeTui {
    private $host = 'http://sdk.open.api.igexin.com/apiex.htm';

    //测试
    private $appkey = 'Ngq0xOTlzo6vyy0iIBkCw3';
    private $appid = 'zrCloUNS3jAGFgkPkG9397';
    private $mastersecret = 'cPlg35x4OY6jQeNdiXKjK';
    public function pushToAndroidApp($title, $content, $message) {
        $igt = new \IGeTui($this->host, $this->appkey, $this->mastersecret);
        //$igt = new IGeTui('',APPKEY,MASTERSECRET);//此方式可通过获取服务端地址列表判断最快域名后进行消息推送，每10分钟检查一次最快域名
        //消息模版：
        // 1.TransmissionTemplate:透传功能模板
        // 2.LinkTemplate:通知打开链接功能模板
        // 3.NotificationTemplate：通知透传功能模板
        // 4.NotyPopLoadTemplate：通知弹框下载功能模板
        //$template = IGtNotyPopLoadTemplateDemo();
        //$template = IGtLinkTemplateDemo();
//            $template = IGtNotificationTemplateDemo();
        //$template = IGtTransmissionTemplateDemo();

        $template =  new \IGtTransmissionTemplate();
        $template->set_appId($this->appid);                   //应用appid
        $template->set_appkey($this->appkey);                 //应用appkey
        $template->set_transmissionType(2);            //透传消息类型
        $template->set_transmissionContent(json_encode($message));//透传内容

        //个推信息体
        //基于应用消息体
        $message = new \IGtAppMessage();
        $message->set_isOffline(true);
        $message->set_offlineExpireTime(3600*12*1000);//离线时间单位为毫秒，例，两个小时离线为3600*1000*2
        $message->set_data($template);
        $message->set_PushNetWorkType(0);//设置是否根据WIFI推送消息，1为wifi推送，0为不限制推送
        $message->set_speed(1000);// 设置群推接口的推送速度，单位为条/秒，例如填写100，则为100条/秒。仅对指定应用群推接口有效。
        $message->set_appIdList(array($this->appid));
//        $message->set_provinceList(array('浙江','上海','北京'));
//        $message->set_tagList(array('开心'));
        $res = $igt->pushMessageToApp($message);
        return $res;
    }
}