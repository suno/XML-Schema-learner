<?php
/**
 * Schema learning
 *
 * This file is part of SchemaLearner.
 *
 * SchemaLearner is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; version 3 of the License.
 *
 * SchemaLearner is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with SchemaLearner; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @package Core
 * @version $Revision: 1236 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */

/**
 * Hidden Markov Model
 *
 * Class representing a Hidden Markov Model.
 *
 * @package Core
 * @version $Revision: 1236 $
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GPL
 */
class slHiddenMarkovModel implements Countable
{
    /**
     * Array with node labels in the HMM
     * 
     * @var array
     */
    protected $labels = array();

    /**
     * Array with the transistion probabilities as an adjazence matrix.
     * 
     * @var array
     */
    protected $start = array();

    /**
     * Array with the transistion probabilities as an adjazence matrix.
     * 
     * @var array
     */
    protected $transistion = array();

    /**
     * Array with the emission probabilities as an adjazence matrix.
     * 
     * @var array
     */
    protected $emission = array();

    /**
     * Construct Hidden Markov Model froms set of labels
     * 
     * @param int $states 
     * @param array $labels 
     * @return void
     */
    public function __construct( $states, array $labels )
    {
        $this->labels      = array_values( $labels );
        $this->start       = array_fill( 0, $states, 1 / $states );
        $this->transistion = array_fill( 0, $states, array_fill( 0, $states, 1 / $states ) );
        $this->emission    = array_fill( 0, $states, array_fill( 0, count( $labels ), 1 / count( $labels ) ) );
    }

    /**
     * Get Transistion probability from item $x to item $y
     * 
     * @param int $x 
     * @param int $y 
     * @return float
     */
    public function getTransition( $x, $y )
    {
        if ( !isset( $this->transistion[$x] ) ||
             !isset( $this->transistion[$x][$y] ) )
        {
            throw new OutOfBoundsException();
        }

        return $this->transistion[$x][$y];
    }

    /**
     * Get label of item
     * 
     * @param int $x 
     * @return mixed
     */
    public function getLabel( $x )
    {
        if ( !isset( $this->labels[$x] ) )
        {
            throw new OutOfBoundsException();
        }

        return $this->labels[$x];
    }

    /**
     * Get number of lables, aka, the dimension of the HMM
     * 
     * @return int
     */
    public function count()
    {
        return count( $this->transistion );
    }

    /**
     * Get array of random values
     *
     * Returns an array of the specified size conatining random values, which 
     * sum up to 1.
     * 
     * @param int $size 
     * @return array
     */
    protected function getRandomArray( $size )
    {
        $return = array();
        $left   = 1;
        for ( $i = 0; $i < $size; ++$i )
        {
            if ( $i === ( $size - 1 ) )
            {
                $return[] = $left;
                break;
            }

            $return[] = $v = mt_rand( 0, $left * 10000 ) / 10000;
            $left    -= $v;
        }

        return $return;
    }

    /**
     * Randomize HMM
     *
     * Create random tansistion probabilities for the HMM.
     * 
     * @return void
     */
    public function randomize()
    {
        $states = count( $this->transistion );
        for ( $i = 0; $i < $states; ++$i )
        {
            $this->transistion[$i] = $this->getRandomArray( $states );
            $this->emission[$i]    = $this->getRandomArray( count( $this->labels ) );
        }

        $this->start = $this->getRandomArray( $states );
    }
}

