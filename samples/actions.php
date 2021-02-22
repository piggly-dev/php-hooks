<?php
use Piggly\Hooks\Hook;

// ## Closures ##
Hook::action('sentences.line', function () { echo "I am line 01\n"; });
Hook::action('sentences.message', function ( $message ) { echo sprintf("Message: %s\n", $message); });

Hook::run('sentences', 'Peace and Love');

// ## Functions ##
Hook::reset();

function line () { echo "I am line 02\n"; }
function message ( $message ) { echo sprintf("Message: %s\n", $message); }

Hook::action('sentences.line', 'line');
Hook::action('sentences.message', 'message');

Hook::run('sentences', 'Peace and Love');

// ## Static Class ##
Hook::reset();

class StaticSentences
{
	public static function line()
	{ echo "I am line 03\n"; }

	public static function message($message)
	{ echo sprintf("Message: %s\n", $message); }
}

Hook::action('sentences.line', StaticSentences::class, 'line');
Hook::action('sentences.message', StaticSentences::class, 'message');

Hook::run('sentences', 'Peace and Love');

// ## Class ##
Hook::reset();

class Sentences
{
	protected $sentences;

	public function __construct()
	{ $this->sentences = []; }

	public function getSentences () : array
	{ return $this->sentences; }

	public function line()
	{ echo ($this->sentences[] = "I am line 04\n"); }

	public function message($message)
	{ echo ($this->sentences[] = sprintf("Message: %s\n", $message)); }
}

$sentences = new Sentences();

Hook::action('sentences.line', $sentences, 'line');
Hook::action('sentences.message', $sentences, 'message');

Hook::run('sentences', 'Peace and Love');