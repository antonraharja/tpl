<?php

/*
 * The MIT License
 *
 * Copyright 2014 Anton Raharja <antonrd at gmail dot com>.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
*/

namespace Antonraharja;

/**
 * Dead simple PHP template engine
 *
 * @author Anton Raharja
 * @link https://github.com/antonraharja/tpl
 */
class Tpl
{
	
	private $_filename;
	private $_content;
	private $_result;
	private $_compiled;
	
	public $dir_template = './templates';
	public $dir_cache = './cache';
	
	public $name;
	public $vars = array();
	public $ifs = array();
	public $loops = array();
	public $injects = array();


	// private methods
	
	/**
	 * Template string manipulation
	 * @param  string $key     Template key
	 * @param  string $val     Template value
	 */
	private function _setString($key, $val) {
		$this->_result = str_replace('{' . $key . '}', $val, $this->_result);
	}
	
	/**
	 * Template loop manipulation
	 * @param  string $key     Template key
	 * @param  string $val     Template value
	 */
	private function _setArray($key, $val) {
		preg_match("/<loop\." . $key . ">(.*?)<\/loop\." . $key . ">/s", $this->_result, $l);
		
		$loop_content = '';
		$loop = $l[1];
		foreach ($val as $v) {
			$loop_replaced = $loop;
			foreach ($v as $x => $y) {
				$loop_replaced = str_replace('{' . $key . '.' . $x . '}', $y, $loop_replaced);
			}
			$loop_content.= $loop_replaced;
		}
		
		$this->_result = preg_replace("/<loop\." . $key . ">(.*?)<\/loop\." . $key . ">/s", $loop_content, $this->_result);
		$this->_result = str_replace("<loop." . $key . ">", '', $this->_result);
		$this->_result = str_replace("</loop." . $key . ">", '', $this->_result);
	}
	
	/**
	 * Template boolean manipulation
	 * @param  string $key     Template key
	 * @param  string $val     Template value
	 */
	private function _setBool($key, $val) {
		if ($key && !$val) {
			$this->_result = preg_replace("/<if\." . $key . ">(.*?)<\/if\." . $key . ">/s", '', $this->_result);
		}
		$this->_result = str_replace("<if." . $key . ">", '', $this->_result);
		$this->_result = str_replace("</if." . $key . ">", '', $this->_result);
	}
	
	/**
	 * Set content from file
	 */
	private function _setContentFromFile() {
		// empty original template content
		$this->setContent('');

		// check for template file and load it
		if ($filename = $this->getTemplate()) {
			if (file_exists($filename)) {
				$content = trim(file_get_contents($this->_filename));
				$this->setContent($content);
			}
		}
	}
	
	/**
	 * Process original content according to template rules and settings
	 */
	private function _compile() {
		$this->_result = $this->_content;
		
		if ($this->_result) {
			
			if ($this->ifs) {
				foreach ($this->ifs as $key => $val) {
					$this->_setBool($key, $val);
				}
				empty($this->ifs);
			}
			
			if ($this->loops) {
				foreach ($this->loops as $key => $val) {
					$this->_setArray($key, $val);
				}
				empty($this->loops);
			}
			
			if ($this->vars) {
				foreach ($this->vars as $key => $val) {
					$this->_setString($key, $val);
				}
				empty($this->vars);
			}
			
			if (is_array($this->injects)) {
				foreach ($this->injects as $inject) {
					global ${$inject};
				}
				extract($this->injects);
			}
		}
		
		$this->_result = preg_replace("/<if\..*?>(.*?)<\/if\..*?>/s", '', $this->_result);
		$this->_result = preg_replace("/<loop\..*?>(.*?)<\/loop\..*?>/s", '', $this->_result);
		
		$pattern = "\{\{(.*?)\}\}";
		preg_match_all("/" . $pattern . "/", $this->_result, $matches, PREG_SET_ORDER);
		foreach ($matches as $block) {
			$chunk = $block[0];
			$codes = '<?php ' . trim($block[1]) . ' ?>';
			$this->_result = str_replace($chunk, $codes, $this->_result);
		}
		
		// attempt to create cache file for this template in storage directory
		$cache_file = md5($this->_filename) . '.compiled';
		$cache = $this->dir_cache . '/' . $cache_file;
		$fd = @fopen($cache, 'w+');
		@fwrite($fd, $this->_result);
		@fclose($fd);
		
		// when failed, try to create in /tmp
		if (!file_exists($cache)) {
			$cache = '/tmp/' . $cache_file;
			$fd = @fopen($cache, 'w+');
			@fwrite($fd, $this->_result);
			@fclose($fd);
		}
		
		// if template cache file created then include it, else use eval() to compile
		if (file_exists($cache)) {
			ob_start();
			include $cache;
			$this->_compiled = ob_get_contents();
			ob_end_clean();
			@unlink($cache);
		} else {
			ob_start();
			eval('?>' . $this->_result . '<?php ');
			$this->_compiled = ob_get_contents();
			ob_end_clean();
		}
	}


	// public methods
	
	/**
	 * Compile template
	 */
	function compile() {

		// if no setContent() then load the from file
		if (! $this->getContent()) {

			// if no setTemplate() then use default template file
			if (! $this->getTemplate()) {
				$this->setTemplate($this->dir_template . '/' . $this->name . '.html');
			}

			$this->_setContentFromFile();
		}

		$this->_compile();
	}
	
	/**
	 * Set full path template file
	 * @param string $filename Filename
	 */
	function setTemplate($filename) {
		$this->_filename = $filename;
	}
	
	/**
	 * Get full path template filename
	 * @return string Filename
	 */
	function getTemplate() {
		return $this->_filename;
	}

	/**
	 * Set original template content
	 * @param string $content Original content
	 */
	function setContent($content) {
		$this->_content = $content;
	}

	/**
	 * Get original template content
	 * @return string Original content
	 */
	function getContent() {
		return $this->_content;
	}
	
	/**
	 * Get manipulated template content
	 * @return string Manipulated content
	 */
	function getResult() {
		return $this->_result;
	}
	
	/**
	 * Get compiled template content
	 * @return string Compiled content
	 */
	function getCompiled() {
		return $this->_compiled;
	}
}
