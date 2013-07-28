<?php

require_once 'Word.php';
require_once 'Classifier.php';
require_once 'NaiveBayes.php';

function sampleTrain($cl) {
    $cl->train('Nobody owns the water.', 'good');
    $cl->train('the quick rabbit jumps fences', 'good');
    $cl->train('buy pharmaceuticals now', 'bad');
    $cl->train('make money quick in the online casino', 'bad');
    $cl->train('the quick brown fox jumps', 'good');
}

$word = new Word();

//$cl = new Classifier($word);
//sampleTrain($cl);

//echo $cl->fcount('quick', 'good') . "\n";
//echo $cl->fcount('quick', 'bad') . "\n";

//echo $cl->fprob('quick', 'good') . "\n";

//echo $cl->weightedprob('money', 'good') . "\n";
//sampleTrain($cl);
//echo $cl->weightedprob('money', 'good') . "\n";

$cl = new NaiveBayes($word);
sampleTrain($cl);

echo $cl->classify('quick rabbit', $default='unknown') . "\n";
echo $cl->classify('quick money', $default='unknown') . "\n";

$cl->setThreshold('bad', 3.0);
echo $cl->classify('quick money', $default='unknown') . "\n";

for($i=0; $i<10; $i++) {
    sampleTrain($cl);
}
echo $cl->classify('quick money', $default='unknown') . "\n";
