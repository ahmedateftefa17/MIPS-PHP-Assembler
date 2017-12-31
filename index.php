<!DOCTYPE html>
<html>
<head>
  <title>Assembler</title>
  <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
  <link rel="stylesheet" type="text/css" href="css/style.css">
</head>
<body>
  <div class="holder">
    <div class="form-code">
      <div style="width: 300px;"></div>
     <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" class="form-group">
      <textarea class="form-control" rows="27" cols="36" name="data"><?php echo $data = isset($_POST['data']) ? $_POST['data'] : ""; ?></textarea>
      <!-- <input type="radio" name="from" value="input" <?php if(!isset($_POST['from']) || $_POST['from'] == "input") echo "checked"; ?>/> Input<br>
      <input type="radio" name="from" value="file" <?php if(isset($_POST['from']) && $_POST['from'] == "file") echo "checked"; ?>/> File<br> -->
      <input type="submit" class="btn btn-success" value="submit" />
    </form>
  </div>
    <div class="output text-center">
      <h3>Binary Instructions</h3>
      <hr>
      <?php
        include 'assembler.php';
        $data = isset($_POST['data']) ? $_POST['data'] : "";
        if(isset($_POST['from']) && $_POST['from'] == "file")
          $assembled_data = assembler();
        else $assembled_data = assembler($data);
        $assembled_data_len = strlen($assembled_data);
        for ($i = 0; $i < $assembled_data_len; $i++) {
          if($assembled_data[$i] == "1")
            echo "<span style=\"color: rgb(231,70,67);\">" . $assembled_data[$i] . "</span>";
          elseif($assembled_data[$i] == "0")
            echo "<span style=\"color: rgb(52,152,213);\">" . $assembled_data[$i] . "</span>";
          elseif($assembled_data[$i] == "\n")
            echo "<br>";
        }
      ?>
    </div>
    <div class="output text-center">
      <h3>Hexa Instructions</h3>
      <hr>
      <?php
        if(isset($assembled_data)) {
          $lines = explode("\r\n", $assembled_data);
          foreach ($lines as $line) if(!empty($line))
            echo sprintf("%08X<br>", bindec($line));
        }
      ?>
    </div>
    <div class="output text-left">
      <h3>Supported Instructions</h3>
      <hr>
      <h4><u>Register-format instructions</u></h4>
      add $t1, $t2, $t3<br>
      sub $t1, $t2, $t3<br>
      and $t1, $t2, $t3<br>
      or $t1, $t2, $t3<br>
      nor $t1, $t2, $t3<br>
      slt $t1, $t2, $t3<br>
      sll $t1, $t2, 5<br>
      srl $t1, $t2, 5<br>
      jr $ra<br>
      <h4><u>Jump-format instructions</u></h4>
      j L1<br>
      jal L1<br>
      <h4><u>Immediate-format instructions</u></h4>
      addi $t1, $t2, 15<br>
      andi $t1, $t2, 15<br>
      ori $t1, $t2, 15<br>
      slti $t1, $t2, 15<br>
      lui $t1, 15<br>
      lw $t1, 15 ($t2)<br>
      lh $t1, 15 ($t2)<br>
      lb $t1, 15 ($t2)<br>
      sw $t1, 15 ($t2)<br>
      sh $t1, 15 ($t2)<br>
      sb $t1, 15 ($t2)<br>
      beq $t1, $t2, L1<br>
      bne $t1, $t2, L1<br>
    </div>
  </div>
  <script type="text/javascript" src="js/bootstrap.min.js"></script>
  <script type="text/javascript" src="js/script.js"></script>
</body>
</html>
