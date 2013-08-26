<?php
/**
 * Copyright (c) 2002-2005, Richard Heyes
 * All rights reserved.
 *
 * PHP version 4, 5
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *  o Redistributions of source code must retain the above copyright
 *    notice, this list of conditions and the following disclaimer.
 *  o Redistributions in binary form must reproduce the above copyright
 *    notice, this list of conditions and the following disclaimer in the
 *    documentation and/or other materials provided with the distribution.
 *  o The names of the authors may not be used to endorse or promote
 *    products derived from this software without specific prior written
 *    permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * @category File
 * @package  File_SearchReplace
 * @author   Richard Heyes <richard@phpguru.org>
 * @license  http://www.opensource.org/licenses/bsd-license.php BSD
 * @version  CVS: $Id: SearchReplace.php 304758 2010-10-25 10:29:02Z clockwerx $
 * @link     http://pear.php.net/File_SearchReplace
 */

/**
 * Search and Replace Utility
 *
 * @category File
 * @package  File_SearchReplace
 * @author   Richard Heyes <richard@phpguru.org>
 * @license  http://www.opensource.org/licenses/bsd-license.php BSD
 * @link     http://pear.php.net/File_SearchReplace
 */
class File_SearchReplace
{

    // {{{ Properties (All private)

    var $find;
    var $replace;
    var $files;
    var $directories;
    var $include_subdir;
    var $ignore_lines;
    var $ignore_sep;
    var $occurences;
    var $search_function;
    var $php5;
    var $last_error;

    // }}}
    // {{{ Constructor

    /**
     * Sets up the object
     *
     * @param string $find           The string/regex to find.
     * @param string $replace        The string/regex to replace $find with.
     * @param array  $files          The file(s) to perform this operation on.
     * @param array  $directories    The directories to perform this operation on.
     * @param bool   $include_subdir If performing on directories, whether to
     *                               traverse subdirectories.
     * @param array  $ignore_lines   Ignore lines beginning with any of the strings
     *                               in this array. This
     *                               feature only works with the "normal" search.
     *
     * @access public
     */
    function File_SearchReplace($find, $replace, $files, $directories = '',
                                $include_subdir = true, $ignore_lines = array())
    {

        $this->setFind($find);
        $this->setReplace($replace);
        $this->setFiles($files);

        $this->setDirectories($directories);
        $this->setIncludeSubdir($include_subdir);

        $this->setIgnoreLines((array) $ignore_lines);

        $this->occurences      = 0;
        $this->search_function = 'search';
        $this->php5            = substr(PHP_VERSION, 0, 1) == 5;
        $this->last_error      = '';

    }

    // }}}
    // {{{ getNumOccurences()

    /**
     * Accessor to return the number of occurences found.
     *
     * @access public
     * @return int Number of occurences found.
     */
    function getNumOccurences()
    {
        return $this->occurences;
    }

    // }}}
    // {{{ getLastError()

    /**
     * Accessor for retrieving last error.
     *
     * @access public
     * @return string The last error that occurred, if any.
     */
    function getLastError()
    {
        return $this->last_error;
    }

    // }}}
    // {{{ setFind()

    /**
     * Accessor for setting find variable.
     *
     * @param mixed $find The string/regex to find, or array of strings
     *
     * @access public
     * @return void
     */
    function setFind($find)
    {
        $this->find = $find;
    }

    // }}}
    // {{{ setReplace()

    /**
     * Accessor for setting replace variable.
     *
     * @param mixed $replace The string/regex to replace the find
     * string/regex with, or array of strings
     *
     * @access public
     * @return void
     */
    function setReplace($replace)
    {
        $this->replace = $replace;
    }

    // }}}
    // {{{ setFiles()

    /**
     * Accessor for setting files variable.
     *
     * @param array $files The file(s) to perform this operation on.
     *
     * @access public
     * @return void
     */
    function setFiles($files)
    {
        $this->files = $files;
    }

    // }}}
    // {{{ setDirectories()

    /**
     * Accessor for setting directories variable.
     *
     * @param array $directories The directories to perform this operation on.
     *
     * @access public
     * @return void
     */
    function setDirectories($directories)
    {
        $this->directories = $directories;
    }

    // }}}
    // {{{ setIncludeSubdir

    /**
     * Accessor for setting include_subdir variable.
     *
     * @param bool $include_subdir Whether to traverse subdirectories or not.
     *
     * @access public
     * @return void
     */
    function setIncludeSubdir($include_subdir)
    {
        $this->include_subdir = $include_subdir;
    }

    // }}}
    // {{{ setIgnoreLines()

    /**
     * Accessor for setting ignore_lines variable.
     *
     * @param array $ignore_lines Ignore lines beginning with any of the
     *                            strings in this array. This
     *                            feature only works with the "normal" search.
     *
     * @access public
     * @return void
     */
    function setIgnoreLines($ignore_lines)
    {
        $this->ignore_lines = $ignore_lines;
    }

    // }}}
    // {{{ setSearchFunction()

    /**
     * Function to determine which search function is used.
     *
     * Can be any one of:
     *  normal - Default search. Goes line by line. Ignore lines feature
     *           only works with this type.
     *  quick  - Uses str_replace for straight replacement throughout
     *           file. Quickest of the lot.
     *  preg   - Uses preg_replace(), so any valid regex
     *
     * @param string $search_function The search function that should be used.
     *
     * @access public
     * @return void
     */
    function setSearchFunction($search_function)
    {
        switch($search_function) {
        case 'normal':
            $this->search_function = 'search';
            return true;
            break;

        case 'quick' :
            $this->search_function = 'quickSearch';
            return true;
            break;

        case 'preg'  :
            $this->search_function = 'pregSearch';
            return true;
            break;

        default      :
            $this->last_error = 'Invalid search function specified';
            return false;
            break;
        }
    }

    // }}}
    // {{{ search()

    /**
     * Default ("normal") search routine.
     *
     * @param string $filename The filename to search and replace upon.
     *
     * @access private
     * @return array Will return an array containing the new file contents
     *               and the number of occurences.
     *               Will return false if there are no occurences.
     */
    function search($filename)
    {
        $occurences = 0;

        $lines = file($filename);

        // just for the sake of catching occurences
        $local_find    = $this->getFind();
        $local_replace = $this->getReplace();

        if (empty($this->ignore_lines) && $this->php5) { // PHP5 acceleration
            $lines = str_replace($local_find, $local_replace,
                                      $lines, $occurences);

        } else { // str_replace() doesn't return number of occurences in PHP4
                 // so we need to count them manually and/or filter strings
            $ignore_lines_num = count($this->ignore_lines);



            foreach ($lines as $i => $line) {

                if ($ignore_lines_num > 0) {
                    for ($j = 0; $j < $ignore_lines_num; $j++) {
                        $text = substr($line, 0, strlen($this->ignore_lines[$j]));
                        if ($text == $this->ignore_lines[$j]) {
                            continue 2;
                        }
                    }
                }

                if ($this->php5) {
                    $lines[$i] = str_replace($local_find, $local_replace,
                                                  $line, $counted);

                    $occurences += $counted;
                } else {
                    foreach ($local_find as $fk => $ff) {
                        $occurences += substr_count($line, $ff);
                        if (!is_array($local_replace)) {
                            $fr = $local_replace;
                        } else {
                            $fr = "";
                            if (isset($local_replace[$fk])) {
                                $fr = $local_replace[$fk];
                            }
                        }
                        $lines[$i] = str_replace($ff, $fr, $line);
                    }
                }
            }

        }

        if ($occurences > 0) {
            return array($occurences, implode('', $lines));
        }

        return false;
    }

    // }}}
    // {{{ quickSearch()

    /**
     * Quick search routine.
     *
     * @param string $filename The filename to search and replace upon.
     *
     * @access private
     * @return array Will return an array containing the new file contents
     *               and the number of occurences.
     *               Will return false if there are no occurences.
     */
    function quickSearch($filename)
    {

        clearstatcache();

        $file = file_get_contents($filename);

        $local_find    = $this->getFind();
        $local_replace = $this->getReplace();

        $occurences = 0;

        // logic is the same as in str_replace function with one exception:
        //   if <search> is a string and <replacement> is an array - substitution
        //   is done from the first element of array. str_replace in this case
        //   usualy fails with notice and returns "ArrayArrayArray..." string
        // (this exclusive logic of SearchReplace will not work for php5, though,
        // because I haven't decided yet whether it is bug or feature)

        if ($this->php5) {
            $file = str_replace($local_find, $local_replace, $file, $counted);

            $occurences += $counted;
        } else {
            foreach ($local_find as $fk => $ff) {
                $occurences += substr_count($file, $ff);
                if (!is_array($local_replace)) {
                    $fr = $local_replace;
                } else {
                    $fr = isset($local_replace[$fk]) ? $local_replace[$fk] : "";
                }
                $file = str_replace($ff, $fr, $file);
            }
        }

        if ($occurences > 0) {
            return array($occurences, $file);
        }

        return false;

    }

    // }}}
    // {{{ pregSearch()

    /**
     * Preg search routine.
     *
     * @param string $filename The filename to search and replace upon.
     *
     * @access private
     * @return array Will return an array containing the new file contents
     *               and the number of occurences.
     *               Will return false if there are no occurences.
     */
    function pregSearch($filename)
    {

        clearstatcache();

        $file = file_get_contents($filename);

        $local_find    = $this->getFind();
        $local_replace = $this->getReplace();

        $occurences = 0;

        foreach ($local_find as $fk => $ff) {
            $occurences += preg_match_all($ff, $file, $matches);
            if (!is_array($local_replace)) {
                $fr = $local_replace;
            } else {
                $fr = isset($local_replace[$fk]) ? $local_replace[$fk] : "";
            }
            $file = preg_replace($ff, $fr, $file);
        }

        if ($occurences > 0) {
            return array($occurences, $file);
        }

        return false;

    }

    // }}}
    // {{{ writeout()

    /**
     * Function to writeout the file contents.
     *
     * @param string $filename The filename of the file to write.
     * @param string $contents The contents to write to the file.
     *
     * @access private
     * @return void
     */
    function writeout($filename, $contents)
    {

        if ($fp = @fopen($filename, 'w')) {
            flock($fp, 2);
            fwrite($fp, $contents);
            flock($fp, 3);
            fclose($fp);
        } else {
            $this->last_error = 'Could not open file: '.$filename;
        }

    }

    // }}}
    // {{{ doFiles()

    /**
     * Function called by doSearch() to go through any files that need searching.
     *
     * @param string $ser_func The search function to use.
     *
     * @access private
     * @return void
     */
    function doFiles($ser_func)
    {
        if (!is_array($this->files)) {
            $this->files = explode(',', $this->files);
        }

        foreach ($this->files as $file) {
            if ($file == '.' OR $file == '..') {
                continue;
            }

            if (is_dir($file)) {
                continue;
            }

            $newfile = $this->$ser_func($file);
            if (is_array($newfile)) {
                $this->writeout($file, $newfile[1]);
                $this->occurences += $newfile[0];
            }
        }
    }

    // }}}
    // {{{ doDirectories()

    /**
     * Function called by doSearch() to go through any directories that
     * need searching.
     *
     * @param string $ser_func The search function to use.
     *
     * @access private
     * @return void
     */
    function doDirectories($ser_func)
    {
        if (!is_array($this->directories)) {
            $this->directories = explode(',', $this->directories);
        }

        foreach ($this->directories as $directory) {
            $dh = opendir($directory);
            while ($file = readdir($dh)) {
                if ($file == '.' OR $file == '..') {
                    continue;
                }

                if (is_dir($directory.$file)) {
                    if ($this->include_subdir) {
                        $this->directories[] = $directory.$file.'/';
                        continue;
                    } else {
                        continue;
                    }
                }

                $newfile = $this->$ser_func($directory.$file);
                if (is_array($newfile) == true) {
                    $this->writeout($directory.$file, $newfile[1]);
                    $this->occurences += $newfile[0];
                }
            }
        }
    }

    // }}}
    // {{{ doSearch()

    /**
     * This starts the search/replace off. The behavior of this function will likely
     * to be changed in future versions to work in read only mode. If you want to do
     * actual replace with writing files - use doReplace method instead.
     *
     * @access public
     * @return void
     */
    function doSearch()
    {
        $this->doReplace();
    }

    // }}}
    // {{{ doReplace()

    /**
     * This starts the search/replace off. Call this to do the replace.
     * First do whatever files are specified, and/or if directories are specified,
     * do those too.
     *
     * @access public
     * @return void
     */
    function doReplace()
    {
        $this->occurences = 0;
        if (!empty($this->find)) {
            if (!empty($this->files)) {
                $this->doFiles($this->search_function);
            }

            if (!empty($this->directories)) {
                $this->doDirectories($this->search_function);
            }
        }
    }

    // }}}

    /**
     * Helper method to ensure we always have an array of things to find.
     *
     * @access public
     * @return array
     */
    function getFind()
    {
        return array_values((array) $this->find);
    }

    /**
     * Helper method to fetch replace
     *
     * @access public
     * @return mixed
     */
    function getReplace()
    {
        if (is_array($this->replace)) {
            return array_values($this->replace);
        }

        return $this->replace;
    }
}
?>
