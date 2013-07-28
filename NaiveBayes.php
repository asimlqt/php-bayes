<?php
/*
 * Copyright (C) 2012 Asim Liaquat
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *                               
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>. 
 */

class NaiveBayes extends Classifier {

    /**
     * Thresholds
     * 
     * @var array
     */
    private $thresholds = array();

    public function docprob($item, $category) {
        $features = $this->getfeatures->getWords($item);
        
        $p = 1;
        foreach($features as $feature) {
            $p *= $this->weightedProbability($feature, $category);
        }
        return $p;
    }
    
    /**
     * prob
     * 
     * @param  string $item     
     * @param  string $category 
     * 
     * @return float
     */
    public function prob($item, $category) {
        $catprob = $this->numItemsInCategory($category) / $this->totalCount();
        $docprob = $this->docprob($item, $category);
        return $docprob*$catprob; 
    }
    
    /**
     * Set threshold
     * 
     * @param string $category 
     * @param float  $threshold
     *
     * @return void
     */
    public function setThreshold($category, $threshold) {
        $this->thresholds[$category] = $threshold;
    }
    
    /**
     * Get threshold
     * 
     * @param string $category
     * 
     * @return float
     */
    public function getThreshold($category) {
        if(!array_key_exists($category, $this->thresholds))
            return 1.0;
        return $this->thresholds[$category];
    }
    
    public function classify($item, $default=null) {
        $probs = array();
        $max = 0.0;
        $best = null;
        
        foreach($this->getCategories() as $cat) {
            $probs[$cat] = $this->prob($item, $cat);
            if($probs[$cat] > $max) {
                $max = $probs[$cat];
                $best = $cat;
            }
        }
        
        foreach($probs as $cat => $prob) {
            if($cat == $best) continue;
            if($probs[$cat]*$this->getThreshold($best) > $probs[$best]) return $default;
        }
        
        return $best;
    }
}
