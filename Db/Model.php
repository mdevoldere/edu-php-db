<?php 

namespace Md\Db;

/** Class Model
 *
 * @author   MDevoldere 
 * @version  1.0.0
 * @access   public
 */
abstract class Model
{
    public function __construct(array $data = []) 
    {
        foreach($data as $k => $v) {
            if(property_exists($this, $k)) {
                $this->{$k} = $v;
            }
        }
    }

    /**
     * Check if model data is valid
     * @return bool true if model is valid, false otherwrise
     */
    public function validate(): bool 
    {
        return true;
    }

    /**
     * Get the current Model data as array
     * @return array the current model data
     */
    public function toArray(): array 
    {
        foreach($this as $k => $v) {
            $r[$k] = $v;
        }
        return $r;
    }
}
