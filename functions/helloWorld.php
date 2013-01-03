<?php

class HelloWorldFunction
{
	public function run ($params) {
		// Create output data
		$dataout='<p class="cellGreen">Hello World!</p>';

		$num=(int)$params['num'];
		$dataout .= ' The '.($num+1).' parameters were the number '.$num .' preceded by <br />';
		foreach ($params AS $key=>$val){
			if ($val != $num) $dataout.=$val.'<br />';
		}
		return $dataout;
	}
}

return "HelloWorldFunction";

?>
