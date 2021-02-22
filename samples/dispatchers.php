<?php
use Piggly\Hooks\Hook;

// ## Closures ##

Hook::dispatch('sentences.name::1', function ( $message ) { echo sprintf("Your name: %s\n", $message); }, 'Caique');
Hook::dispatch('sentences.prog::1', function ( $message ) { echo sprintf("Progamming Language: %s\n", $message); }, 'PHP');

Hook::run('sentences', 'Peace and Love');

// ## Functions ##
Hook::reset();

function name ( $message ) { echo sprintf("Your name: %s\n", $message); }
function prog ( $message ) { echo sprintf("Progamming Language: %s\n", $message); }

Hook::dispatch('sentences.name::1', 'name', 'Alpha');
Hook::dispatch('sentences.prog::1', 'prog', 'JS');

Hook::run('sentences', 'Peace and Love');

// ## Static Class ##
Hook::reset();

class StaticSentences
{
	public static function name($message)
	{ echo sprintf("Your name: %s\n", $message); }

	public static function prog($message)
	{ echo sprintf("Progamming Language: %s\n", $message); }
}

Hook::dispatch('sentences.name::1', StaticSentences::class, 'name', 'Beta');
Hook::dispatch('sentences.prog::1', StaticSentences::class, 'prog', 'Go');

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

	public function name($message)
	{ echo ($this->sentences[] = sprintf("Your name: %s\n", $message)); }

	public function prog($message)
	{ echo ($this->sentences[] = sprintf("Progamming Language: %s\n", $message)); }
}

$sentences = new Sentences();

Hook::dispatch('sentences.name::1', $sentences, 'name', 'Charlie');
Hook::dispatch('sentences.prog::1', $sentences, 'prog', 'Python');

Hook::run('sentences', 'Peace and Love');