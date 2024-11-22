import { registerBlockType } from '@wordpress/blocks';
import { TextControl }  from '@wordpress/components';

registerBlockType(
    'pg/basic',
    {
        title: "Basic Block",
        description: "Este es nuestro primer bloque",
        icon: "smiley",
        category: "layout",
        attributes: {
            content: {
                type: "string",
                default: "Hello World"
            }
        },
        edit: (props) => {
            const {attributes: {content}, setAttributes, clasName, isSelected } = props
            const handlerOnChangeInput = (newContent) => {
                setAttributes ( { content:newContent } )
            }
            return <TextControl
            label = "Complete el input"
            value = {content}
            onChange = {handlerOnChangeInput}
            />
        },
        save: (props) => <h2>{props.attributes.content}</h2>
    }
)