<?php

for ($i = 1; $i <= 100; $i++) {
	$symbol = (100 === $i ? PHP_EOL : ',');

	if (0 === ($i % 3) && 0 === ($i % 5)) {
		echo 'foobar' . $symbol;
	}
	elseif (0 === ($i % 3)) {
		echo 'foo' . $symbol;
	}
	else if (0 === ($i % 5)) {
		echo 'bar' . $symbol;
	}
	else {
		echo $i . $symbol;
	}
}
