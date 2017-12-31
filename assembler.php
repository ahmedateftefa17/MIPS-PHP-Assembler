<?php

  include "constants.php";

  function assembler($data = NULL){
    if($data === NULL) {
      $file = fopen("instructions.asm", "r");
      $data = fread($file, filesize("instructions.asm"));
      fclose($file);
    }

    $instructions_bin = "";
    $instructions_array_2d = [];

    $instructions_array = explode("\r\n", $data);
    $ins_count = 0;
    if(count($instructions_array) > 0) foreach ($instructions_array as $instruction_line) {
      $ins_count++;
      $instruction_line_array = [];
      if(strpos($instruction_line, ":"))
        $instruction_line = trim(substr($instruction_line, strpos($instruction_line, ":")+1));
      $temp = explode(' ', $instruction_line);
      foreach($temp as $temp_){
        $temp_len = strlen($temp_);
        $temp_ = trim($temp_);
        $temp_com_pos = strpos($temp_, ",");
        if($temp_com_pos !== false && $temp_com_pos < $temp_len - 1) {
          $temp_ = $temp_[$temp_len - 1] == "," ? substr($temp_, 0, strlen($temp_) - 1) : $temp_;
          $temp_ = explode(',', $temp_);
          foreach ($temp_ as $temp__) {
            $temp__ = strpos($temp__, ",") ? substr($temp__, 0, strlen($temp__) - 1) : $temp__;
            $instruction_line_array[] = $temp__;
          }
        } else {
          $temp_pra_pos = strpos($temp_, "(");
          if($temp_pra_pos !== false && $temp_pra_pos !== 0) {
            $temp_ = substr($temp_, 0, $temp_len - 1);
            $temp_ = explode("(", $temp_);
            $instruction_line_array[] = $temp_[0];
            $instruction_line_array[] = $temp_[1];
          } else {
            $temp_ = strpos($temp_, ",") ? substr($temp_, 0, strlen($temp_) - 1) : $temp_;
            if($temp_ == "" || $temp_ == ",") continue;
            $instruction_line_array[] = $temp_[0] == "(" ? substr($temp_, 1, $temp_len - 2) : $temp_;
          }
        }
      }
      $instructions_array_2d[] = $instruction_line_array;
      // echo '<pre>'; var_dump($instruction_line_array); echo '</pre>';

      if(count($instruction_line_array) < 1) continue;
      if(array_key_exists($instruction_line_array[0], $GLOBALS['instructions']["RA"])){
        $instructions_bin .= "000000";
        $instructions_bin .= $GLOBALS['registers'][$instruction_line_array[2]];
        $instructions_bin .= $GLOBALS['registers'][$instruction_line_array[3]];
        $instructions_bin .= $GLOBALS['registers'][$instruction_line_array[1]];
        $instructions_bin .= "00000";
        $instructions_bin .= $GLOBALS['instructions']["RA"][$instruction_line_array[0]];
        $instructions_bin .= "\r\n";
      } elseif(array_key_exists($instruction_line_array[0], $GLOBALS['instructions']["RS"])){
        $instructions_bin .= "000000";
        $instructions_bin .= $GLOBALS['registers'][$instruction_line_array[2]];
        $instructions_bin .= "00000";
        $instructions_bin .= $GLOBALS['registers'][$instruction_line_array[1]];
        $instructions_bin .= sprintf("%'.05b", (int) $instruction_line_array[3]);
        $instructions_bin .= $GLOBALS['instructions']["RS"][$instruction_line_array[0]];
        $instructions_bin .= "\r\n";
      } elseif(array_key_exists($instruction_line_array[0], $GLOBALS['instructions']["IA"])){
        $instructions_bin .= $GLOBALS['instructions']["IA"][$instruction_line_array[0]];
        $instructions_bin .= $GLOBALS['registers'][$instruction_line_array[2]];
        $instructions_bin .= $GLOBALS['registers'][$instruction_line_array[1]];
        if((int) $instruction_line_array[3] >= 0)
          $instructions_bin .= sprintf("%'.016b", (int) $instruction_line_array[3]);
        else $instructions_bin .= substr(sprintf("%b", (int) $instruction_line_array[3]), 48, 64);
        $instructions_bin .= "\r\n";
      } elseif(array_key_exists($instruction_line_array[0], $GLOBALS['instructions']["IB"])){
        $instructions_bin .= sprintf("%'.032b", 32);
        $instructions_bin .= "\r\n" . sprintf("%'.032b", 32);
        $instructions_bin .= "\r\n";
        for($i = 0; $i < count($instructions_array); $i++)
          if(strpos(trim($instructions_array[$i]), $instruction_line_array[3]) === 0)
            $instruction_line_array[3] = $i - $ins_count;
        $instructions_bin .= $GLOBALS['instructions']["IB"][$instruction_line_array[0]];
        $instructions_bin .= $GLOBALS['registers'][$instruction_line_array[1]];
        $instructions_bin .= $GLOBALS['registers'][$instruction_line_array[2]];
        if((int) $instruction_line_array[3] >= 0)
          $instructions_bin .= sprintf("%'.016b", (int) $instruction_line_array[3]);
        else $instructions_bin .= substr(sprintf("%b", (int) $instruction_line_array[3]), 48, 64);
        $instructions_bin .= "\r\n" . sprintf("%'.032b", 32);
        $instructions_bin .= "\r\n" . sprintf("%'.032b", 32);
        $instructions_bin .= "\r\n";
      } elseif(array_key_exists($instruction_line_array[0], $GLOBALS['instructions']["IM"])){
        $instructions_bin .= $GLOBALS['instructions']["IM"][$instruction_line_array[0]];
        $instructions_bin .= $GLOBALS['registers'][$instruction_line_array[3]];
        $instructions_bin .= $GLOBALS['registers'][$instruction_line_array[1]];
        if((int) $instruction_line_array[2] >= 0)
          $instructions_bin .= sprintf("%'.016b", (int) $instruction_line_array[2]);
        else $instructions_bin .= substr(sprintf("%b", (int) $instruction_line_array[2]), 48, 64);
        $instructions_bin .= "\r\n";
      } elseif(array_key_exists($instruction_line_array[0], $GLOBALS['instructions']["JL"])){
        for($i = 0; $i < count($instructions_array); $i++)
          if(strpos(trim($instructions_array[$i]), $instruction_line_array[1]) === 0)
            $instruction_line_array[1] = $i;
        $instructions_bin .= $GLOBALS['instructions']["JL"][$instruction_line_array[0]];
        if((int) $instruction_line_array[1] >= 0)
          $instructions_bin .= sprintf("%'.026b", (int) $instruction_line_array[1]);
        else $instructions_bin .= substr(sprintf("%b", (int) $instruction_line_array[1]), 48, 64);
        $instructions_bin .= "\r\n";
      }
    }
    $file = fopen("instructions.bin", "w");
    fwrite($file, instructions_bin_extender($instructions_bin));
    fclose($file);
    return $instructions_bin;
  }

  function instructions_bin_extender($instructions_bin){
    for ($i = substr_count($instructions_bin, "\r\n"); $i < 64; $i++)
      $instructions_bin .= sprintf("%'.032b", 0) . "\r\n";
    return substr($instructions_bin, 0, strlen($instructions_bin) - 2);
  }
