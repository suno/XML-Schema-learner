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
 * Test class
 */
class slMainChareConverterTests extends PHPUnit_Framework_TestCase
{
    /**
     * Return test suite
     *
     * @return PHPUnit_Framework_TestSuite
     */
	public static function suite()
	{
		return new PHPUnit_Framework_TestSuite( __CLASS__ );
	}

    public function testDisjunction()
    {
        $automaton = new slCountingSingleOccurenceAutomaton();
        $automaton->learn( array( 'a' ) );
        $automaton->learn( array( 'b' ) );

        $converter = new slChareConverter();
        $regexp    = $converter->convertAutomaton( $automaton );
        $this->assertEquals(
            new slRegularExpressionSequence( array(
                new slRegularExpressionRepeated( array(
                    new slRegularExpressionChoice( array( 'a', 'b' ) ),
                ) ),
            ) ),
            $regexp
        );
    }

    public function testConcatenation()
    {
        $automaton = new slCountingSingleOccurenceAutomaton();
        $automaton->learn( array( 'a', 'b' ) );

        $converter = new slChareConverter();
        $regexp    = $converter->convertAutomaton( $automaton );
        $this->assertEquals(
            new slRegularExpressionSequence( array(
                new slRegularExpressionRepeated( array( 'a' ) ),
                new slRegularExpressionRepeated( array( 'b' ) ),
            ) ),
            $regexp
        );
    }

    public function testConvertOrderInsignificant()
    {
        $automaton = new slCountingSingleOccurenceAutomaton();
        $automaton->learn( array( 'a', 'b' ) );
        $automaton->learn( array( 'b', 'a' ) );

        $converter = new slChareConverter();
        $regexp    = $converter->convertAutomaton( $automaton );
        $this->assertEquals(
            new slRegularExpressionSequence( array(
                new slRegularExpressionRepeated( array(
                    new slRegularExpressionChoice( array( 'a', 'b' ) ),
                ) ),
            ) ),
            $regexp
        );
    }

    public function testConvertPaperExample()
    {
        // Example 2. Let W = {abccde, cccad, bfegg, bfehi}.
        $automaton = new slCountingSingleOccurenceAutomaton();
        $automaton->learn( array( 'a', 'b', 'c', 'c', 'd', 'e' ) );
        $automaton->learn( array( 'c', 'c', 'c', 'a', 'd' ) );
        $automaton->learn( array( 'b', 'f', 'e', 'g', 'g' ) );
        $automaton->learn( array( 'b', 'f', 'e', 'h', 'i' ) );

        $converter = new slChareConverter();
        $regexp    = $converter->convertAutomaton( $automaton );
        $this->assertEquals(
            new slRegularExpressionSequence( array(
                new slRegularExpressionRepeated( array(
                    new slRegularExpressionChoice( array( 'a', 'b', 'c' ) ),
                ) ),
                new slRegularExpressionRepeated( array(
                    new slRegularExpressionChoice( array( 'd', 'f' ) ),
                ) ),
                new slRegularExpressionRepeated( array( 'e' ) ),
                new slRegularExpressionRepeated( array( 'g' ) ),
                new slRegularExpressionRepeated( array( 'h' ) ),
                new slRegularExpressionRepeated( array( 'i' ) ),
            ) ),
            $regexp
        );
    }
}

