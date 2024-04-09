// Check Running Instant        {{{
$_lock_file = @fopen('/tmp/pid__'.basename(__FILE__),'c');
$_got_lock = @flock($_lock_file, LOCK_EX | LOCK_NB, $_would_block);
if (false === $_lock_file || (! $_got_lock && ! $_would_block)) {
        throw new Exception(
                'Dont have permission to write or lock file'.PHP_EOL);
}
else if (! $_got_lock && $_would_block) {
        exit(
                'Another Instance is already running; terminating.'.PHP_EOL);
}
@ftruncate($_lock_file, 0);
@fwrite($_lock_file, getmypid()."\n");
// }}}
