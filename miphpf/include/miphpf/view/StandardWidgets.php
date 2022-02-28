<?php
	/**
	 * The standard widgets
	 * @copyright Copyright (c) 2006,2007 Mirchev Ideas Ltd. All rights reserved.
	 * @package MIPHPF
	 */

	/**
	 * Standard text widget
	 * Use it with input type="text" and with textarea
	 * @copyright Copyright (c) 2006,2007 Mirchev Ideas Ltd. All rights reserved.
	 * @package MIPHPF
	 */
	class miTextWidget extends miBaseTextWidget {
		/**
		 * Returns the text widget contents to be displayed
		 * 
		 * @return array
		 */
		function getEditableControl()
		{
			$id = isset($this->_properties['id']) ? ' id="' . $this->_properties['id'] . '"' : '';
			$widget = '<input type="text" name="' . $this->_fieldName . '" value="' . miI18N::htmlEscape($this->_webForm->getFormData($this->_fieldName)) . '"' . $id . '/>';
			return array($this->_fieldName => $widget);
		}
	}
	
	
	/**
	 * Standard checkbox widget
	 * Use it with input type="checkbox"
	 * @copyright Copyright (c) 2006,2007 Mirchev Ideas Ltd. All rights reserved.
	 * @package MIPHPF
	 */
	class miCheckboxWidget extends miBaseCheckboxWidget {
		/**
		 * Returns the checkbox widget contents to be displayed
		 * 
		 * @return array
		 */
		function getEditableControl()
		{
			$id = isset($this->_properties['id']) ? ' id="' . $this->_properties['id'] . '"' : '';
			$checked = $this->getValue() ? ' checked="checked"' : '';
			$widget = '<input type="checkbox" name="' . $this->_fieldName . '"' . $checked . $id . '/>';
			return array($this->_fieldName => $widget);
		}
	}
	
	/**
	 * Standard radio buttons widget
	 * Use it with input type="radio"
	 * 
	 * This class supports a group of radio buttons
	 * @copyright Copyright (c) 2006,2007 Mirchev Ideas Ltd. All rights reserved.
	 * @package MIPHPF
	 */
	class miRadioWidget extends miBaseRadioWidget {
		
		/**
		 * Returns the widget contents to be displayed
		 * 
		 * @return array
		 */
		public function getEditableControl()
		{
			$value = $this->getValue();
			
			$widget = '';
			foreach ($this->_radioButtons as $key => $option) {
				$checked = ($value == $key) ? ' checked="checked"' : '';
				$widget .= '<label><input type="radio" name="' . $this->_fieldName . '" value="' . $key . '"' . $checked . '/>' . miI18N::htmlEscape($option) . '</label>';
			}
			return array($this->_fieldName => $widget);
		}
	}
	
	/**
	 * Standard select widget
	 * Use it with select tag
	 * @copyright Copyright (c) 2006,2007 Mirchev Ideas Ltd. All rights reserved.
	 * @package MIPHPF
	 */
	class miSelectWidget extends miBaseSelectWidget {
		
		/**
		 * Returns the widget contents to be displayed
		 * 
		 * @return array
		 */
		public function getEditableControl()
		{
			$value = $this->getValue();
			
			$id = isset($this->_properties['id']) ? ' id="' . $this->_properties['id'] . '"' : '';
			$class = isset($this->_properties['class']) ? ' class="' . $this->_properties['class'] . '"' : '';
			$html = '<select name="' . $this->_fieldName . '"' . $id . $class . '>';
			foreach ($this->_options as $key => $option)
				$html .= '<option value="' . miI18N::htmlEscape($key) . '"' . ($key == $value?' selected="selected"':'') . '>' . miI18N::htmlEscape($option) . '</option>';
			$html .= '</select>';
			return array($this->_fieldName => $html);
		}
	}
?>