<?php

namespace Eazpl\Enums;

enum FieldOrientationEnums: string
{
    case _0 = 'N'; // default: 0 degree
    case _90 = 'R'; // 90 degree clockwise
    case _180 = 'I'; // 180 degree clockwise
    case _270 = 'B'; // 270 degree clockwise
}
