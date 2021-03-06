<?php
namespace FengruzhuoDebug\Model;

class Debug{
	
	/** debug 函数
	@param mix data 调试的数据
	@param string method 写文件方法,传'w'则覆盖,传'a'则续写
	@param string memo 摘要信息
	@param array aCustomParam 自定义参数 xmp=>是否使用xmp来渲染
	@author feng
	@return bool
	*/
	static function dump($data, $memo='None', $aCustomParam=array('datatag'=>'xmp'),$method="a")
	{
		/********************配置区域***************************/
		$aFengruzhuoDebugConfig = require(dirname(__FILE__)."/../../../config/module.config.php");
		$cacheFile = $aFengruzhuoDebugConfig['debugconfig']['cachepath'];//debug文件存放地址
		$debugFlag = $aFengruzhuoDebugConfig['debugconfig']['enable'];//调试标识. 0=>不记录, 1=>记录
		$sJqueryPath = dirname(__FILE__)."/jquery.min.js";
		/********************配置区域 end***************************/

		if($debugFlag == 0 && $method=='a')return false;
		if($debugFlag == 0 && $method=='w'){
			file_put_contents($cacheFile, "<html><head></head><body>['debugconfig']['enable'] 's value  is FALSE in this module config.php, set TRUE when debuging </body></html>");
			return false;
		}
		if(isset($_SERVER['REQUEST_URI']) && is_string($_SERVER['REQUEST_URI'])){
			if(preg_match("/.*FengruzhuoDebug.*/i", $_SERVER['REQUEST_URI'])){
				return false;
			}
		}
		$DebugFilePath = $_SERVER["PHP_SELF"];
		if($method=='w'){
			$sJquery = file_get_contents($sJqueryPath);

			$oldContent = '<html xmlns="http://www.w3.org/1999/xhtml"><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8" /><title>DEBUGING LOG</title><script type="text/javascript">'.$sJquery.'</script>';
			$sStyle = <<<EOT
<style type="text/css">
body {
	margin: 0px;
	padding: 10px;
	height: 100%;
}

body, th, td {
	font-family: Tahoma, Verdana, Arial, Helvetica, sans-serif;
	font-size: 12px;
	color: #333;
}
div.info{
	width: 880px;
	word-wrap: break-word;
}
.clear{
	clear: both;
	display: block;
}
</style>
EOT;
			$sScript = <<<EOT
			<script>
			\$(function() {
				var _oMemo = {all:{label:'all', total:$("div.block").length}};
				\$("div.block").each(function(){
					if(typeof _oMemo[$(this).attr('_k')] == 'undefined')
						_oMemo[$(this).attr('_k')] = {};
					if(typeof _oMemo[$(this).attr('_k')]['total'] == 'undefined'){
						_oMemo[$(this).attr('_k')]['total'] = 1;
						_oMemo[$(this).attr('_k')]['label'] = $(this).attr('_l');
					}else
						_oMemo[$(this).attr('_k')]['total'] += 1;
				})
				var sUl = "";
				for(var k in _oMemo){
					sUl += '<li><a _k="'+k+'" href="javascript:void()" >'+_oMemo[k]['label']+'('+_oMemo[k]['total']+')</a></li>';
				}
				$('div#tabs').html("<ul>"+sUl+"</ul><div  style=\"position:absolute;top:10px;right:20px;\" class='allinfoSwith'><a href='javascript:void()' >All Fold/Unfold</a></div>");
				$('div#tabs li a').click(function(){
					var _showK = $(this).attr('_k');
					var sAnchor = location.href.substring(location.href.lastIndexOf('#'))
					location.href = location.href.replace(sAnchor, "#"+_showK);
					if(_showK == 'all'){
						$('div.block').show();
					}else{
						$('div.block').hide();
						$('div.block[_k="'+_showK+'"]').show();
					}
				});
				if(location.href.lastIndexOf('#') != -1){
					var sAnchor = location.href.substring(location.href.lastIndexOf('#'))
					$('div#tabs li a[_k="'+sAnchor.replace("#", "")+'"]').click()
				}
				$('div.block span.infoswitch a').click(function(){
					var _o = $(this).parents('div.block').find('div.info').eq(0);
						_o.toggle();
				});
				var allinfoSwithIndex = 0;
				$('div.allinfoSwith a').click(function(){
					allinfoSwithIndex%2==0 ? $('div.info').hide() : $('div.info').show();
					allinfoSwithIndex++;
				});
//				\$( "#tabs" ).tabs();
			});
			</script>
EOT;
			$oldContent .=$sStyle.'</head><body>
			<div>
				<b>\\'.__CLASS__."::".__FUNCTION__."(\$var, 'memo')".';</b>
				<p>use the above code in your code as var_dump(), the output will be rewrote to this file instead of printing directly.
			</div>
			<hr>
			<div id="tabs"></div></body></html>'.$sScript;
		}else{
			$oldContent = (file_exists($cacheFile)) ? file_get_contents($cacheFile) : "";
		}
		$sBlockHTML = "\n\n\n<div class='block' _k='".md5($memo)."' _l='".$memo."'><span style='display:none'><------orderIndex-------></span>";
		$orderIndex = substr_count($oldContent, '<------orderIndex------->');
		
		list($em, $es) = explode(' ', empty($time) ? microtime() : $time);
        $timespan = (float)$em + (float)$es;

		 $str = $sBlockHTML;
		 $str .= "<span  class='no' style='color:blue;'>NO</span>:\t".++$orderIndex."\n\n";
		 $str .= "<span  style='color:blue;'>Timespan</span>:\t".$timespan."\n\n";
		 $str .= "\t<span  style='color:blue;'>Date</span>:\t".date("Y-m-d H:i:s")."\n";
		 $str .= "\t<span  style='color:blue;'>File</span>:\t".$DebugFilePath."\n";
		 $str .= "<br/><span class='memo' style='color:blue;'>Memo</span>:\t".$memo."<br>\n";
		 $str .= "----------------------------------------<span class='infoswitch'><a  href='javascript:void()' >Fold/Unfold</a></span> <a href='#tabs' >top</a>\n<div class='info'>";
		 $Tab = "";
		ob_start();
		if(is_string($data))
			echo $data;
		else
			var_export($data);
		$a = ob_get_contents();
		ob_end_clean();
		if(isset($aCustomParam['datatag']))
			$str .= "<".$aCustomParam['datatag'].">";
		$str .= $a;
		if(isset($aCustomParam['datatag']))
			$str .= "</".$aCustomParam['datatag'].">";
		 $str .= "</div>\n<hr></div>\n\n\n";

		$oldContent = str_replace("</body>", $str."</body>", $oldContent);
		file_put_contents($cacheFile, $oldContent);
		return 1;
	}
}
