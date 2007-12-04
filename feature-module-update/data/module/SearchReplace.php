<?php
// +-----------------------------------------------------------------------+
// | Copyright (c) 2002-2005, Richard Heyes                                |
// | All rights reserved.                                                  |
// |                                                                       |
// | Redistribution and use in source and binary forms, with or without    |
// | modification, are permitted provided that the following conditions    |
// | are met:                                                              |
// |                                                                       |
// | o Redistributions of source code must retain the above copyright      |
// |   notice, this list of conditions and the following disclaimer.       |
// | o Redistributions in binary form must reproduce the above copyright   |
// |   notice, this list of conditions and the following disclaimer in the |
// |   documentation and/or other materials provided with the distribution.|
// | o The names of the authors may not be used to endorse or promote      |
// |   products derived from this software without specific prior written  |
// |   permission.                                                         |
// |                                                                       |
// | THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS   |
// | "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT     |
// | LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR |
// | A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT  |
// | OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, |
// | SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT      |
// | LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, |
// | DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY |
// | THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT   |
// | (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE |
// | OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.  |
// |                                                                       |
// +-----------------------------------------------------------------------+
// | Author: Richard Heyes <richard@phpguru.org>                           |
// +-----------------------------------------------------------------------+
//
// $Id$
//
// Search and Replace Utility
//

/**
 * Search and Replace Utility
 *
 *
 * @author  Richard Heyes <richard@phpguru.org>
 * @version 1.0
 * @package File
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
     * @access public
     * @param string $find                      The string/regex to find.
     * @param string $replace                   The string/regex to replace $find with.
     * @param array  $files                     The file(s) to perform this operation on.
     * @param array  $directories    (optional) The directories to perform this operation on.
     * @param bool   $include_subdir            If performing on directories, whether to traverse subdirectories.
     * @param array  $ignore_lines              Ignore lines beginning with any of the strings in this array. This
     *                                          feature only works with the "normal" search.
     */
    function File_SearchReplace($find, $replace, $files, $directories = '', $include_subdir = TRUE, $ignore_lines = array())
    {

        $this->find            = $find;
        $this->replace         = $replace;
        $this->files           = $files;
        $this->directories     = $directories;
        $this->include_subdir  = $include_subdir;
        $this->ignore_lines    = (array) $ignore_lines;

        $this->occurences      = 0;
        $this->search_function = 'search';
        $this->php5            = (substr(PHP_VERSION, 0, 1) == 5) ? TRUE : FALSE;
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
     * @access public
     * @param string $find The string/regex to find.
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
     * @access public
     * @param string $replace The string/regex to replace the find string/regex with.
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
     * @access public
     * @param array $files The file(s) to perform this operation on.
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
     * @access public
     * @param array $directories The directories to perform this operation on.
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
     * @access public
     * @param bool $include_subdir Whether to traverse subdirectories or not.
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
     * @access public
     * @param array $ignore_lines Ignore lines beginning with any of the strings in this array. This
     *                            feature only works with the "normal" search.
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
     * @access public
     * @param string The search function that should be used. Can be any one of:
     *               normal - Default search. Goes line by line. Ignore lines feature only works with this type.
     *               quick  - Uses str_replace for straight replacement throughout file. Quickest of the lot.
     *               preg   - Uses preg_replace(), so any regex valid with this function is valid here.
     *               ereg   - Uses ereg_replace(), so any regex valid with this function is valid here.
     */
    function setSearchFunction($search_function)
    {
        switch($search_function) {
        case 'normal': $this->search_function = 'search';
            return TRUE;
            break;

        case 'quick' : $this->search_function = 'quickSearch';
            return TRUE;
            break;

        case 'preg'  : $this->search_function = 'pregSearch';
            return TRUE;
            break;

        case 'ereg'  : $this->search_function = 'eregSearch';
            return TRUE;
            break;

        default      : $this->last_error      = 'Invalid search function specified';
            return FALSE;
            break;
        }
    }

    // }}}
    // {{{ search()

    /**
     * Default ("normal") search routine.
     *
     * @access private
     * @param string $filename The filename to search and replace upon.
     * @return array Will return an array containing the new file contents and the number of occurences.
     *               Will return FALSE if there are no occurences.
     */
    function search($filename)
    {
        $occurences = 0;
        $file_array = file($filename);

        if (empty($this->ignore_lines) && $this->php5) { // PHP5 acceleration
            $file_array = str_replace($this->find, $this->replace, $file_array, $occurences);

        } else { // str_replace() doesn't return number of occurences in PHP4
                 // so we need to count them manually and/or filter strings
            $ignore_lines_num = count($this->ignore_lines);

            // just for the sake of catching occurences
            $local_find    = array_values((array) $this->find);
            $local_replace = (is_array($this->replace)) ? array_values($this->replace) : $this->replace;

            for ($i=0; $i < count($file_array); $i++) {

                if ($ignore_lines_num > 0) {
                    for ($j=0; $j < $ignore_lines_num; $j++) {
                        if (substr($file_array[$i],0,strlen($this->ignore_lines[$j])) == $this->ignore_lines[$j]) continue 2;
                    }
                }

                if ($this->php5) {
                    $file_array[$i] = str_replace($this->find, $this->replace, $file_array[$i], $counted);
                    $occurences += $counted;
                } else {
                    foreach ($local_find as $fk => $ff) {
                        $occurences += substr_count($file_array[$i], $ff);
                        if (!is_array($local_replace)) {
                            $fr = $local_replace;
                        } else {
                            $fr = (isset($local_replace[$fk])) ? $local_replace[$fk] : "";
                        }
                        $file_array[$i] = str_replace($ff, $fr, $file_array[$i]);
                    }
                }
            }

        }
        if ($occurences > 0) $return = array($occurences, implode('', $file_array)); else $return = FALSE;
        return $return;

    }

    // }}}
    // {{{ quickSearch()

    /**
     * Quick search routine.
     *
     * @access private
     * @param string $filename The filename to search and replace upon.
     * @return array Will return an array containing the new file contents and the number of occurences.
     *               Will return FALSE if there are no occurences.
     */
    function quickSearch($filename)
    {

        clearstatcache();

        $file          = fread($fp = fopen($filename, 'r'), max(1, filesize($filename))); fclose($fp);
        $local_find    = array_values((array) $this->find);
        $local_replace = (is_array($this->replace)) ? array_values($this->replace) : $this->replace;

        $occurences    = 0;

        // logic is the same as in str_replace function with one exception:
        //   if <search> is a string and <replacement> is an array - substitution
        //   is done from the first element of array. str_replace in this case
        //   usualy fails with notice and returns "ArrayArrayArray..." string
        // (this exclusive logic of SearchReplace will not work for php5, though,
        // because I haven't decided yet whether it is bug or feature)

        if ($this->php5) {
            $file_array[$i] = str_replace($this->find, $this->replace, $file_array[$i], $counted);
            $occurences += $counted;
        } else {
            foreach ($local_find as $fk => $ff) {
                $occurences += substr_count($file, $ff);
                if (!is_array($local_replace)) {
                    $fr = $local_replace;
                } else {
                    $fr = (isset($local_replace[$fk])) ? $local_replace[$fk] : "";
                }
                $file = str_replace($ff, $fr, $file);
            }
        }

        if ($occurences > 0) $return = array($occurences, $file); else $return = FALSE;
        return $return;

    }

    // }}}
    // {{{ pregSearch()

    /**
     * Preg search routine.
     *
     * @access private
     * @param string $filename The filename to search and replace upon.
     * @return array Will return an array containing the new file contents and the number of occurences.
     *               Will return FALSE if there are no occurences.
     */
    function pregSearch($filename)
    {

        clearstatcache();

        $file       = fread($fp = fopen($filename, 'r'), max(1, filesize($filename))); fclose($fp);
        $local_find    = array_values((array) $this->find);
        $local_replace = (is_array($this->replace)) ? array_values($this->replace) : $this->replace;

        $occurences = 0;

        foreach($local_find as $fk => $ff) {
            $occurences += preg_match_all($ff, $file, $matches);
            if (!is_array($local_replace)) {
                $fr = $local_replace;
            } else {
                $fr = (isset($local_replace[$fk])) ? $local_replace[$fk] : "";
            }
            $file = preg_replace($ff, $fr, $file);
        }

        if ($occurences > 0) $return = array($occurences, $file); else $return = FALSE;
        return $return;

    }

    // }}}
    // {{{ eregSearch()

    /**
     * Ereg search routine.
     *
     * @access private
     * @param string $filename The filename to search and replace upon.
     * @return array Will return an array containing the new file contents and the number of occurences.
     *               Will return FALSE if there are no occurences.
     */
    function eregSearch($filename)
    {

        clearstatcache();

        $file = fread($fp = fopen($filename, 'r'), max(1, filesize($filename))); fclose($fp);
        $local_find    = array_values((array) $this->find);
        $local_replace = (is_array($this->replace)) ? array_values($this->replace) : $this->replace;

        $occurences = 0;

        foreach($local_find as $fk => $ff) {
            $occurences += count(split($ff, $file)) - 1;
            if (!is_array($local_replace)) {
                $fr = $local_replace;
            } else {
                $fr = (isset($local_replace[$fk])) ? $local_replace[$fk] : "";
            }
            $file = ereg_replace($ff, $fr, $file);
        }

        if ($occurences > 0) $return = array($occurences, $file); else $return = FALSE;
        return $return;

    }

    // }}}
    // {{{ writeout()
    
    /**
     * Function to writeout the file contents.
     *
     * @access private
     * @param string $filename The filename of the file to write.
     * @param string $contents The contents to write to the file.
     */
    function writeout($filename, $contents)
    {

        if ($fp = @fopen($filename, 'w')) {
            flock($fp,2);
            fwrite($fp, $contents);
            flock($fp,3);
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
     * @access private
     * @param string $ser_func The search function to use.
     */
    function doFiles($ser_func)
    {
        if (!is_array($this->files)) $this->files = explode(',', $this->files);
        for ($i=0; $i<count($this->files); $i++) {
            if ($this->files[$i] == '.' OR $this->files[$i] == '..') continue;
            if (is_dir($this->files[$i]) == TRUE) continue;
            $newfile = $this->$ser_func($this->files[$i]);
            if (is_array($newfile) == TRUE){
                $this->writeout($this->files[$i], $newfile[1]);
                $this->occurences += $newfile[0];
            }
        }
    }

    // }}}
    // {{{ doDirectories()

    /**
     * Function called by doSearch() to go through any directories that need searching.
     *
     * @access private
     * @param string $ser_func The search function to use.
     */
    function doDirectories($ser_func)
    {
        if (!is_array($this->directories)) $this->directories = explode(',', $this->directories);
        for ($i=0; $i<count($this->directories); $i++) {
            $dh = opendir($this->directories[$i]);
            while ($file = readdir($dh)) {
                if ($file == '.' OR $file == '..') continue;

                if (is_dir($this->directories[$i].$file) == TRUE) {
                    if ($this->include_subdir == TRUE) {
                        $this->directories[] = $this->directories[$i].$file.'/';
                        continue;
                    } else {
                        continue;
                    }
                }

                $newfile = $this->$ser_func($this->directories[$i].$file);
                if (is_array($newfile) == TRUE) {
                    $this->writeout($this->directories[$i].$file, $newfile[1]);
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
     */
    function doReplace()
    {
        $this->occurences = 0;
        if ($this->find != '') {
            if ((is_array($this->files) AND count($this->files) > 0) OR $this->files != '') $this->doFiles($this->search_function);
            if ($this->directories != '')                                                   $this->doDirectories($this->search_function);
        }
    }
    
    // }}}

}
?>
