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

class Classifier {

    /**
     * [$fc description]
     * 
     * @var array
     */
    private $featureCategory = array();

    /**
     * Category count.
     * 
     * @var array
     */
    private $categoryCount = array();

    protected $getfeatures;

    /**
     * Constructor 
     * 
     * @param [type] $getfeatures [description]
     */
    public function __construct($getfeatures) {
        $this->getfeatures = $getfeatures;
    }
    
    /**
     * Increase the count of a feature/category pair.
     * 
     * @param  [type] $f   [description]
     * @param  [type] $cat [description]
     * 
     * @return void
     */
    public function incrementFeatureCategoryCount($feature, $category) {
        if(!array_key_exists($feature, $this->featureCategory))
            $this->featureCategory[$feature] = array();
            
        if(!array_key_exists($category, $this->featureCategory[$feature]))
            $this->featureCategory[$feature][$category] = 0;
            
        $this->featureCategory[$feature][$category] += 1;
    }
    
    /**
     * Increase the count of a category.
     * 
     * @param  [type] $cat [description]
     * 
     * @return void
     */
    public function incrementCategoryCount($category) {
        if(!array_key_exists($category, $this->categoryCount))
            $this->categoryCount[$category] = 0;
        $this->categoryCount[$category] += 1;
    }
    
    /**
     * The number of times a feature has appeared in a category.
     * 
     * @param  [type] $f   [description]
     * @param  [type] $cat [description]
     * 
     * @return float
     */
    public function featureCount($feature, $category) {
        if(array_key_exists($feature, $this->featureCategory) && array_key_exists($category, $this->featureCategory[$feature]))
            return floatval($this->featureCategory[$feature][$category]);
        return 0.0;
    }
    
    /**
     * The number of items in a category.
     * 
     * @param  [type] $cat [description]
     * 
     * @return int
     */
    public function numItemsInCategory($category) {
        if(array_key_exists($category, $this->categoryCount))
            return floatval($this->categoryCount[$category]);
        return 0.0;
    }
    
    /**
     * The total number of items
     * 
     * @return int
     */
    public function totalCount() {
        return array_sum($this->categoryCount);
    }
    
    /**
     * Get all categories.
     * 
     * @return array
     */
    public function getCategories() {
        return array_keys($this->categoryCount);
    }
    
    /**
     * Train.
     *
     * Explodes the item into words and then loops over each one and increments the feature-category count
     * and then increments the category count.
     * 
     * @param  [type] $item [description]
     * @param  [type] $cat  [description]
     * 
     * @return [type]       [description]
     */
    public function train($item, $category) {
        $features = $this->getfeatures->getWords($item);
        foreach($features as $feature) {
            $this->incrementFeatureCategoryCount($feature, $category);
        }
        $this->incrementCategoryCount($category);
    }
    
    /**
     * Feature probability - calculates the probability that a feature is in a particular category.
     * 
     * @param  string $feature
     * @param  string $category
     * 
     * @return float
     */
    public function featureProbability($feature, $category) {
        if($this->numItemsInCategory($category) === 0.0) return 0.0;
        return ($this->featureCount($feature, $category) / $this->numItemsInCategory($category));
    }
    
    /**
     * Weighted probability
     * 
     * @param  [type] $feature
     * @param  [type] $category
     * @param  float  $weight
     * @param  float  $assumedProbability
     * 
     * @return float
     */
    public function weightedProbability($feature, $category, $weight=1.0, $assumedProbability=0.5) {
         $basicprob = $this->featureProbability($feature, $category);
        
        $totals = 0;
        foreach($this->getCategories() as $cat) {
            $totals += $this->featureCount($feature, $cat);
        }
        
        $bp = (($weight*$assumedProbability) + ($totals*$basicprob)) / ($weight+$totals);
        return $bp;
    }
}