<?php

/**
 * Move-To-Front transform
 *
 * @author Robin Schuil <r.schuil@gmail.com>
 * @version 0.9.0
 *
 */
class MTF {
	
	/**
	 * Encode a string
	 *
	 * @param string $s
	 * @return string
	 */
	function encode( $s ) {
		$result = '';
		// Initialize an array of characters in order (0..255)
		$list = range( 0, 255 );
		// For each character in $s:
		for( $b=0; $b<strlen($s); $b++ ) {
			$byte = ord( $s{$b} );
			// Find the byte in $list
			$index = array_search( $byte, $list );
			// Output the index
			$result .= chr( $index );
			// Move array values down
			for( ; $index!=0; $index-- ) {
				$list[$index] = $list[$index-1];
			}
			// Put current byte at index 0
			$list[0] = $byte;
		}
		return $result;
	}
	
	/**
	 * Decode a MTF-encoded string
	 *
	 * @param string $s
	 * @return string
	 */
	function decode( $s ) {
		$result = '';
		// Initialize an array of characters in order (0..255)
		$list = range( 0, 255 );
		// For each character in $s:
		for( $b=0; $b<strlen($s); $b++ ) {
			$index = ord( $s{$b} );
			// Get the byte by index from $list
			$byte = $list[$index];
			// Output the byte
			$result .= chr( $byte );
			// Move array values down
			for( ; $index!=0; $index-- ) {
				$list[$index] = $list[$index-1];				
			}
			// Put current byte at index 0
			$list[0] = $byte;
		}
		return $result;
	}
	
}

?>