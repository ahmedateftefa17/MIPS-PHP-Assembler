<?php

  include "constants.php";

  function assembler($request){
    $data = isset($request['assembly']) ? $request['assembly'] : "";
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
        else $instructions_bin .= substr(sprintf("%b", (int) $instruction_line_array[3]), 16, 32);
        $instructions_bin .= "\r\n";
      } elseif(array_key_exists($instruction_line_array[0], $GLOBALS['instructions']["IB"])){
        for($i = 0; $i < count($instructions_array); $i++)
          if(strpos(trim($instructions_array[$i]), $instruction_line_array[3]) === 0)
            $instruction_line_array[3] = $i - $ins_count;
        $instructions_bin .= $GLOBALS['instructions']["IB"][$instruction_line_array[0]];
        $instructions_bin .= $GLOBALS['registers'][$instruction_line_array[1]];
        $instructions_bin .= $GLOBALS['registers'][$instruction_line_array[2]];
        if((int) $instruction_line_array[3] >= 0)
          $instructions_bin .= sprintf("%'.016b", (int) $instruction_line_array[3]);
        else $instructions_bin .= substr(sprintf("%b", (int) $instruction_line_array[3]), 16, 32);
        $instructions_bin .= "\r\n";
      } elseif(array_key_exists($instruction_line_array[0], $GLOBALS['instructions']["IM"])){
        $instructions_bin .= $GLOBALS['instructions']["IM"][$instruction_line_array[0]];
        $instructions_bin .= $GLOBALS['registers'][$instruction_line_array[3]];
        $instructions_bin .= $GLOBALS['registers'][$instruction_line_array[1]];
        if((int) $instruction_line_array[2] >= 0)
          $instructions_bin .= sprintf("%'.016b", (int) $instruction_line_array[2]);
        else $instructions_bin .= substr(sprintf("%b", (int) $instruction_line_array[2]), 16, 32);
        $instructions_bin .= "\r\n";
      } elseif(array_key_exists($instruction_line_array[0], $GLOBALS['instructions']["JL"])){
        for($i = 0; $i < count($instructions_array); $i++)
          if(strpos(trim($instructions_array[$i]), $instruction_line_array[1]) === 0)
            $instruction_line_array[1] = $i;
        $instructions_bin .= $GLOBALS['instructions']["JL"][$instruction_line_array[0]];
        if((int) $instruction_line_array[1] >= 0)
          $instructions_bin .= sprintf("%'.026b", (int) $instruction_line_array[1]);
        else $instructions_bin .= substr(sprintf("%b", (int) $instruction_line_array[1]), 16, 32);
        $instructions_bin .= "\r\n";
      }
    }
    $instructions = fopen("IMemory.bin", "w");
    fwrite($instructions, bin_extender($instructions_bin, 1024));
    fclose($instructions);
    if($data !== "") {
      $RegisterFile = fopen("RegisterFile.bin", "w");
      fwrite($RegisterFile, sprintf("%'.032b\r\n", 0));
      for($i = 1; $i < 32; $i++)
        fwrite($RegisterFile, sprintf("%'.032b\r\n", $request["register_file_$i"]));
      fclose($RegisterFile);
      $DMemory = fopen("DMemory.bin", "w");
      fwrite($DMemory, sprintf("%'.032b\r\n", 0));
      for($i = 1; $i < 1024; $i++)
        fwrite($DMemory, sprintf("%'.032b\r\n", $request["data_memory_$i"]));
      fclose($DMemory);
    }
    echo shell_exec("iverilog -o compiled *.v && vvp compiled");
    return $instructions_bin;
  }

  function bin_extender($instructions_bin, $length){
    for ($i = substr_count($instructions_bin, "\r\n"); $i < $length; $i++)
      $instructions_bin .= sprintf("%'.032b\r\n", 0);
    return substr($instructions_bin, 0, strlen($instructions_bin) - 2);
  }
