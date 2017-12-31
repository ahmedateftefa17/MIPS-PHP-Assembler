<?php

  $instructions = [
    "RA" => [
      "add" => sprintf("%'.06b", 32),
      "sub" => sprintf("%'.06b", 34),
      "and" => sprintf("%'.06b", 36),
      "or"  => sprintf("%'.06b", 37),
      "nor" => sprintf("%'.06b", 39),
      "slt" => sprintf("%'.06b", 42),
    ],
    "RS" => [
      "sll" => sprintf("%'.06b", 00),
      "srl" => sprintf("%'.06b", 02),
    ],
    "IA" => [
      "addi"=> sprintf("%'.06b", 8),
      "andi"=> sprintf("%'.06b", 12),
      "ori" => sprintf("%'.06b", 13),
      "slti"=> sprintf("%'.06b", 10),
      "lui" => sprintf("%'.06b", 15),
    ],
    "IB" => [
      "beq" => sprintf("%'.06b", 04),
      "bne" => sprintf("%'.06b", 05),
    ],
    "IM" => [
      "lw"  => sprintf("%'.06b", 35),
      "lh"  => sprintf("%'.06b", 33),
      "lb"  => sprintf("%'.06b", 32),
      "sw"  => sprintf("%'.06b", 43),
      "sh"  => sprintf("%'.06b", 41),
      "sb"  => sprintf("%'.06b", 40),
    ],
    "JL" =>  [
      "j"   => sprintf("%'.06b", 02),
      "jal" => sprintf("%'.06b", 03),
    ],
    "JR" =>  [
      "jr"  => sprintf("%'.06b", 8),
    ]
  ];

  $registers = [
    '$zero' => sprintf("%'.05b", 0),
    '$at'   => sprintf("%'.05b", 1),
    '$v0'   => sprintf("%'.05b", 2),
    '$v1'   => sprintf("%'.05b", 3),
    '$a0'   => sprintf("%'.05b", 4),
    '$a1'   => sprintf("%'.05b", 5),
    '$a2'   => sprintf("%'.05b", 6),
    '$a3'   => sprintf("%'.05b", 7),
    '$t0'   => sprintf("%'.05b", 8),
    '$t1'   => sprintf("%'.05b", 9),
    '$t2'   => sprintf("%'.05b", 10),
    '$t3'   => sprintf("%'.05b", 11),
    '$t4'   => sprintf("%'.05b", 12),
    '$t5'   => sprintf("%'.05b", 13),
    '$t6'   => sprintf("%'.05b", 14),
    '$t7'   => sprintf("%'.05b", 15),
    '$s0'   => sprintf("%'.05b", 16),
    '$s1'   => sprintf("%'.05b", 17),
    '$s2'   => sprintf("%'.05b", 18),
    '$s3'   => sprintf("%'.05b", 19),
    '$s4'   => sprintf("%'.05b", 20),
    '$s5'   => sprintf("%'.05b", 21),
    '$s6'   => sprintf("%'.05b", 22),
    '$s7'   => sprintf("%'.05b", 23),
    '$t8'   => sprintf("%'.05b", 24),
    '$t9'   => sprintf("%'.05b", 25),
    '$k0'   => sprintf("%'.05b", 26),
    '$k1'   => sprintf("%'.05b", 27),
    '$gp'   => sprintf("%'.05b", 28),
    '$sp'   => sprintf("%'.05b", 29),
    '$fp'   => sprintf("%'.05b", 30),
    '$ra'   => sprintf("%'.05b", 31)
  ];

