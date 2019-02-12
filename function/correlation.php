<?php
$neuron01="npf-F-300001";
$neuron02="npf-F-300003";
$lns01="0 0 0 0 0 0 0 0 0 0 2 6 288 111 0 0 0 0 0 243 167 230 0 0 0 55 239 782 86 298 0 0 0 64 605 54 293 0 0";
$lns02="0 0 0 0 0 36 0 0 0 0 0 0 0 186 0 0 0 0 400 220 455 399 0 0 475 174 236 923 124 82 0 0 0 326 788 0 0 0 0";
echo correlation($neuron01,$neuron02,$lns01,$lns02);
function correlation($neuron01,$neuron02,$lns01,$lns02){
	$r1=ereg_replace(" ",",",trim($lns01)); $r2=ereg_replace(" ",",",trim($lns02));
	$r="'".$neuron01." ".$neuron02."'
	r1 = c(".$r1.")
	r2 = c(".$r2.")
	suppressWarnings(cor.test(r1,r2,method=\"pearson\", alternative = \"greater\"))
	#suppressWarnings(cor.test(r1,r2,method=\"spearman\", alternative = \"greater\"))
	#suppressWarnings(cor.test(r1,r2,method=\"kendall\", alternative = \"greater\"))
	";
	$prgfile_hx = tempnam("/tmp", "track_"); $fp = fopen($prgfile_hx, "w"); fwrite($fp, $r); fclose($fp);
	$cmd="/usr/local/bin/Rscript ".$prgfile_hx; echo $cmd."\n"; $result=shell_exec($cmd); $report=correltionResult($result); unlink($prgfile_hx);
	return $report;
}
         
function correltionResult($result){
  $tmpArr=explode("\n",trim($result));
  $record="";
  for($i=0;$i<count($tmpArr);$i++){
    $tmp=trim($tmpArr[$i]);
    if (substr($tmp,0,3)=="[1]") {
      if (ereg("-M-",$tmp) || ereg("-F-",$tmp)){    
        $record.=substr($tmp,5,-1)." ";
      }
    }elseif (substr($tmp,0,3)=="t =") {
      $smpArr=explode(" ",$tmp);
      if ($smpArr[(count($smpArr)-1)]!="NA") $record.="corP=".$smpArr[(count($smpArr)-1)]." ";
    }elseif (substr($tmp,0,3)=="S =") {
      $smpArr=explode(" ",$tmp);      
      if ($smpArr[(count($smpArr)-1)]!="NA") $record.="rhoP=".$smpArr[(count($smpArr)-1)]." ";
    }elseif (substr($tmp,0,3)=="z =") {
      $smpArr=explode(" ",$tmp);
      if ($smpArr[(count($smpArr)-1)]!="NA") $record.="tauP=".$smpArr[(count($smpArr)-1)]." ";
    }elseif (substr($tmp,0,17)=="sample estimates:") {
      $tmp1=trim($tmpArr[$i+1]); $tmp2=trim($tmpArr[$i+2]);
      if ($tmp1=="cor"){
        if ($tmp2=="NA"){
          $record.="corP=NA cor=".$tmp2." ";
        }else{
          $record.="cor=".$tmp2." ";
        }
      }elseif ($tmp1=="rho"){
        if ($tmp2=="NA"){
          $record.="rohP=NA rho=".$tmp2." ";
        }else{              
          $record.="rho=".$tmp2." ";
        }
      }elseif ($tmp1=="tau"){
        if ($tmp2=="NA"){
          $record.="tauP=NA tau=".$tmp2."\n";        
        }else{      
          $record.="tau=".$tmp2."\n";
        }
      }else{
        echo "error\n";
        exit();
      }
    }
  }
  return $record;
}





?>
