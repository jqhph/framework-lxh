<?php

namespace Lxh\Logger\Formatter;

class TextFormatter extends \Monolog\Formatter\LineFormatter
{
	const SIMPLE_FORMAT = "[%datetime%] %level_name%: %message% %context% %extra%\n";

	public function __construct($format = null, $dateFormat = null, $allowInlineLineBreaks = true, $ignoreEmptyContextAndExtra = false)
	{
		parent::__construct($format, $dateFormat, $allowInlineLineBreaks, $ignoreEmptyContextAndExtra);
	}
}
