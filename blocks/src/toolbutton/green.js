/**
 * Cocoon Blocks
 * @author: yhira
 * @link: https://wp-cocoon.com/
 * @license: http://www.gnu.org/licenses/gpl-2.0.html GPL v2 or later
 */

import {THEME_NAME, LetterToolbarButton } from '../helpers.js';
const { Fragment } = wp.element;
const { __ } = wp.i18n;
const { registerFormatType, toggleFormat } = wp.richText;
const { RichTextShortcut, RichTextToolbarButton } = wp.editor;
const FORMAT_TYPE_NAME = 'cocoon-blocks/green';
import { Icon, pencil } from '@wordpress/icons'
const TITLE = __( '緑色', THEME_NAME );

registerFormatType( FORMAT_TYPE_NAME, {
  title: TITLE,
  tagName: 'span',
  className: 'green',
  edit({isActive, value, onChange}){
    const onToggle = () => onChange(toggleFormat(value,{type:FORMAT_TYPE_NAME}));

    return (
      <Fragment>
        <LetterToolbarButton
          icon={<Icon icon={pencil} size={32} />}
          title={<span className="green">{TITLE}</span>}
          onClick={ onToggle }
          isActive={ isActive }
        />
      </Fragment>
    );
  }
} );
