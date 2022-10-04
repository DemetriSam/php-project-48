<?php
{
    common: {
      + follow: false
        setting1: Value 1
      - setting2: 200
      - setting3: true
      + setting4: blah blah
      setting6: {
        doge: {
          - wow: 
          + wow: so much
      }
          key: value
        + ops: vops
    }
  }
    group1: {
      - baz: bas
      + baz: bars
        foo: bar
  }
    sameNode: {
        setting1: Value 1
        setting2: 200
        setting3: true
      setting6: {
        doge: {
            wow: 
      }
          key: value
    }
  }
}