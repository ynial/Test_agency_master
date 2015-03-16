<?php
/**
 * TOP API: taobao.vmarket.eticket.time.expand request
 * 
 * @author auto create
 * @since 1.0, 2014-12-22 10:36:19
 */
class Taobao_Request_VmarketEticketTimeExpandRequest
{
	/** 
	 * 延长天数，延长时间=当前过期时间+延长天数
	 **/
	private $expandDays;
	
	/** 
	 * 订单ID
	 **/
	private $orderId;
	
	private $apiParas = array();
	
	public function setExpandDays($expandDays)
	{
		$this->expandDays = $expandDays;
		$this->apiParas["expand_days"] = $expandDays;
	}

	public function getExpandDays()
	{
		return $this->expandDays;
	}

	public function setOrderId($orderId)
	{
		$this->orderId = $orderId;
		$this->apiParas["order_id"] = $orderId;
	}

	public function getOrderId()
	{
		return $this->orderId;
	}

	public function getApiMethodName()
	{
		return "taobao.vmarket.eticket.time.expand";
	}
	
	public function getApiParas()
	{
		return $this->apiParas;
	}
	
	public function check()
	{
		
		Taobao_RequestCheckUtil::checkNotNull($this->expandDays,"expandDays");
		Taobao_RequestCheckUtil::checkNotNull($this->orderId,"orderId");
	}
	
	public function putOtherTextParam($key, $value) {
		$this->apiParas[$key] = $value;
		$this->$key = $value;
	}
}
