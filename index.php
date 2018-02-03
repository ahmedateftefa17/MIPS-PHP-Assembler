<?php include 'assembler.php'; ?>
<!DOCTYPE html>
<html>
<head>
  <title>Assembler</title>
  <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
  <link rel="stylesheet" type="text/css" href="css/style.css">
</head>
<body>
  <div class="container-fluid">
    <br>
    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" class="form-group">
      <div class="row">
        <div class="col-md-3">
          <center><h4>Supported Instructions</h4></center>
          <div style="height: 275px; overflow-y: scroll; padding: 15px; overflow-x: hidden; border: 1px solid #ccc;border-radius: 4px;">
            <h5><u>Register-format instructions</u></h5>
            add $t1, $t2, $t3<br>
            sub $t1, $t2, $t3<br>
            and $t1, $t2, $t3<br>
            or $t1, $t2, $t3<br>
            nor $t1, $t2, $t3<br>
            sll $t1, $t2, 5<br>
            srl $t1, $t2, 5<br>
            <h5><u>Jump-format instructions</u></h5>
            j L1<br>
            <h5><u>Immediate-format instructions</u></h5>
            addi $t1, $t2, 15<br>
            andi $t1, $t2, 15<br>
            ori $t1, $t2, 15<br>
            lw $t1, 15 ($t2)<br>
            sw $t1, 15 ($t2)<br>
            beq $t1, $t2, L1<br>
            bne $t1, $t2, L1<br>
          </div>
        </div>
        <div class="col-lg-3">
          <center><h4>Assembly</h4></center>
          <div>
            <textarea class="form-control" rows="13" name="assembly" style="resize: vertical;"><?php echo $assembly = isset($_POST['assembly']) ? $_POST['assembly'] : ""; ?></textarea>
          </div>
        </div>
        <div class="col-lg-3">
          <center><h4>Register File</h4></center>
          <div style="height: 274px; overflow-y: scroll; border: 1px solid #ccc;border-radius: 4px;padding: 15px; overflow-x: hidden;">
            <?php foreach($registers as $register_name => $register_num) {
                    $register_num = bindec($register_num);
                    if($register_num == 0) continue; ?>
              <?php printf("%s: ", $register_name); ?>
              <input class="form-control" type="number" name="register_file_<?php echo $register_num; ?>" value="<?php echo isset($_POST["register_file_$register_num"]) ? $_POST["register_file_$register_num"] : ""; ?>"><br>
            <?php } ?>
          </div>
        </div>
        <div class="col-lg-3">
          <center><h4>Data Memory</h4></center>
          <div style="height: 274px; overflow-y: scroll; border: 1px solid #ccc;border-radius: 4px;padding: 15px; overflow-x: hidden;">
            <?php for($i = 0; $i < 1024; $i++) { ?>
              <?php printf("%04d: ", $i); ?>
              <input class="form-control" type="number" name="data_memory_<?php echo $i; ?>" value="<?php echo isset($_POST["data_memory_$i"]) ? $_POST["data_memory_$i"] : ""; ?>"><br>
            <?php } ?>
          </div>
        </div>
      </div>
      <br>
      <input type="submit" class="btn btn-primary btn-block" value="RUN" />
    </form>
    <br>
    <div class="row">
      <div class="col-md-3">
        <div class="output">
          <h3>Binary Instructions</h3>
          <hr>
          <?php
            $assembled_data = assembler($_POST);
            $assembled_data_len = strlen($assembled_data);
            $lineNo = 0;
            if($assembled_data_len > 5)
              printf("%04d: ", $lineNo);
            for ($i = 0; $i < $assembled_data_len; $i++) {
              if($assembled_data[$i] == "1")
                echo "<span style=\"color: rgb(231,70,67);\">" . $assembled_data[$i] . "</span>";
              elseif($assembled_data[$i] == "0")
                echo "<span style=\"color: rgb(52,152,213);\">" . $assembled_data[$i] . "</span>";
              elseif($assembled_data[$i] == "\n"){
                $lineNo++;
                echo "<br>";
                if($i + 1 < $assembled_data_len)
                  printf("%04d: ", $lineNo);
              }
            }
          ?>
        </div>
      </div>
      <div class="col-md-3">
        <div class="output">
          <h3>Hexa Inst</h3>
          <hr>
          <?php
            if(isset($assembled_data)) {
              $lineNo = 0;
              $lines = explode("\r\n", $assembled_data);
              foreach ($lines as $line) if(!empty($line)) {
                echo sprintf("%04d: %08X<br>", $lineNo, bindec($line));
                $lineNo++;
              }
            }
          ?>
        </div>
      </div>
      <div class="col-md-3">
        <div class="output">
          <h3>Registers File</h3>
          <hr>
          <?php
            if(isset($assembled_data)) {
              $lineNo = 0;
              $registerFile = fopen("outputFileReg.bin", 'r');
              $registerFileData = fread($registerFile, filesize("outputFileReg.bin"));
              fclose($registerFile);
              $lines = explode("\r\n", $registerFileData);
              $register_file_fliped = array_flip($registers);
              foreach ($lines as $line) {
                if($lineNo <= 31)
                  echo sprintf("%s: %08X (%d)<br>", str_replace("_", "&nbsp;", sprintf("%'_5s", $register_file_fliped[sprintf(sprintf("%'.05b", $lineNo))])), bindec($line), bindec($line));
                $lineNo++;
              }
            }
          ?>
        </div>
      </div>
      <div class="col-md-3">
        <div class="output">
          <h3>Data Memory</h3>
          <hr>
          <?php
            if(isset($assembled_data)) {
              $lineNo = 0;
              $dataMemory = fopen("outputFileMem.bin", 'r');
              $dataMemoryData = fread($dataMemory, filesize("outputFileMem.bin"));
              fclose($dataMemory);
              $lines = explode("\r\n", $dataMemoryData);
              foreach ($lines as $line) {
                echo sprintf("%04d: %08X (%d)<br>", $lineNo, bindec($line), bindec($line));
                $lineNo++;
              }
            }
          ?>
        </div>
      </div>
    </div>
  </div>
</body>
</html>
