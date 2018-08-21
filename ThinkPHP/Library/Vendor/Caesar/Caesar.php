<?php

require(dirname(__FILE__) . '/' . 'Bytes.php');
class Caesar
{
	public function getHex(){
		return Bytes::getBytes('0123456789ABCDEF');
	}
	public function getCaesarHexTable(){
		return array(
			Bytes::getBytes('F4C67DBE892A0351'),
			Bytes::getBytes('8E6B4FA59731C20D'),
			Bytes::getBytes('0853DC4EA6972F1B'),
			Bytes::getBytes('9301845CA7DB62EF'),
			Bytes::getBytes('A6C1BE270F53D849'),
			Bytes::getBytes('2BCF4E90738165AD'),
			Bytes::getBytes('1230A49B78C6D5EF'),
			Bytes::getBytes('B3F1806752A9ED4C'),
			Bytes::getBytes('CD041B792EF635A8'),
			Bytes::getBytes('6CD14A5F37892B0E'),
			Bytes::getBytes('54AC179B8632EDF0'),
			Bytes::getBytes('ECB9D3A2F7564018'),
			Bytes::getBytes('4E5AD18067B329CF'),
			Bytes::getBytes('3B4C9AF8507E126D'),
			Bytes::getBytes('79438F1EA560D2CB'),
			Bytes::getBytes('D72AE63BFC590841')
		);
	}
	public function encode($key,$content){
		if(empty($content)){
			return '';
		}
		if(empty($key)){
			return $content;
		}
		$bs=Bytes::getBytes($content);
		$keybs=Bytes::getBytes($key);
		foreach($bs as $i=>$val){
			$bs[$i]^=$keybs[$i%count($keybs)];
		}
		$result='';

		$result=Bytes::bytesToHex($bs);
		return strtoupper($result);
	}
	public function decode($key,$content){
		if(empty($content)){
			return '';
		}
		if(empty($key)){
			return $content;
		}
		$bs=Bytes::hexToBytes($content);
		$keybs=Bytes::getBytes($key);
		foreach($bs as $i=>$val){
			$bs[$i]^=$keybs[$i%count($keybs)];
		}
		$result=Bytes::toStr($bs);
		return $result;
	}
	public function clientEncode($key,$content,$ver)
	{
		$ser=$this->encode($key, $content);
		$index=0;
		$hex=$this->getHex();
		$caesarHexTable=$this->getCaesarHexTable();
		$result='';
		for($i=0;$i<strlen($ser);$i++){
			$c=$key{$index++};
			if($index>=strlen($key)){
				$index=0;
			}
			for($j=0;$j<count($hex);$j++){
				if(ord($ser{$i})==$hex[$j]){
					$result.=chr($caesarHexTable[ord($c)%16][$j]);
				}
			}
		}
		return $result;
	}
	public function clientDecode($key,$content,$ver)
	{
		$index=0;
		$caesarHexTable=$this->getCaesarHexTable();
		$hex=$this->getHex();
		$result='';
		for($i=0;$i<strlen($content);$i++){
			$c=$key{$index++};
			if($index>=strlen($key)){
				$index=0;
			}
			$idx=ord($c)%16;
			for($j=0;$j<count($caesarHexTable[$idx]);$j++){
				if(ord($content{$i})==$caesarHexTable[$idx][$j]){
					$result.=chr($hex[$j]);
				}
			}
		}
		return $this->decode($key, $result);
	}
}
