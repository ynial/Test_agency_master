<?php
/**
 * TOP API: taobao.refund.get request
 * 
 * @author auto create
 * @since 1.0, 2014-12-17 15:38:42
 */
class Taobao_Request_RefundGetRequest
{
	/** 
	 * 需要返回的字段。目前支持有：refund_id, alipay_no, tid, oid, buyer_nick, seller_nick, total_fee, status, created, refund_fee, good_status, has_good_return, payment, reason, desc, num_iid, title, price, num, good_return_time, company_name, sid, address, shipping_type, refund_remind_timeout, refund_phase, refund_version, operation_contraint, attribute, outer_id, sku
	 **/
	private $fields;
	
	/** 
	 * 退款单号<br /> 支持最大值为：9223372036854775807<br /> 支持最小值为：1
	 **/
	private $refundId;
	
	private $apiParas = array();
	
	public function setFields($fields)
	{
		$this->fields = $fields;
		$this->apiParas["fields"] = $fields;
	}

	public function getFields()
	{
		return $this->fields;
	}

	public function setRefundId($refundId)
	{
		$this->refundId = $refundId;
		$this->apiParas["refund_id"] = $refundId;
	}

	public function getRefundId()
	{
		return $this->refundId;
	}

	public function getApiMethodName()
	{
		return "taobao.refund.get";
	}
	
	public function getApiParas()
	{
		return $this->apiParas;
	}
	
	public function check()
	{
		
		Taobao_RequestCheckUtil::checkNotNull($this->fields,"fields");
		Taobao_RequestCheckUtil::checkMaxListSize($this->fields,100,"fields");
		Taobao_RequestCheckUtil::checkNotNull($this->refundId,"refundId");
		Taobao_RequestCheckUtil::checkMaxValue($this->refundId,9223372036854775807,"refundId");
		Taobao_RequestCheckUtil::checkMinValue($this->refundId,1,"refundId");
	}
	
	public function putOtherTextParam($key, $value) {
		$this->apiParas[$key] = $value;
		$this->$key = $value;
	}
}
