<?php $stars=$hall->getAvgRates();
$nstars=5-$stars;
?>
@for($i=0;$i<$stars;$i++)
    <i class="ti-star filled"></i>
@endfor
@for($i=0;$i<$nstars;$i++)
    <i class="ti-star"></i>
@endfor


