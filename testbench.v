`timescale 1ps/1ps
module testbench;
  reg clk = 0;
  integer cycle = 0, i, outputFileReg, outputFileMem;
  always begin
    #100 clk = ~clk;
    if(clk) cycle = cycle + 1;
  end
  cpu cpui(clk);
  initial begin
    $readmemb("RegisterFile.bin", cpui.RegisterFile);
    $readmemb("DMemory.bin", cpui.DMemory);
    $readmemb("IMemory.bin", cpui.IMemory);
    #200000 begin
      outputFileReg = $fopen("outputFileReg.bin");
      for(i = 0; i < 32; i = i + 1)
        $fwrite(outputFileReg, "%b\n", cpui.RegisterFile[i]);
      $fclose(outputFileReg);
      outputFileMem = $fopen("outputFileMem.bin");
      for(i = 0; i < 1024; i = i + 1)
        $fwrite(outputFileMem, "%b\n", cpui.DMemory[i]);
      $fclose(outputFileMem);
      $finish;
    end
  end
endmodule
