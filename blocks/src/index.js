import { registerBlockType } from '@wordpress/blocks';
import { TextControl, PanelBody, PanelRow } from '@wordpress/components';
import { InspectorControls, MediaUpload } from '@wordpress/block-editor';  
import ServerSideRender from '@wordpress/server-side-render'; 

registerBlockType(
    'pg/basic',
    {
        title: "Basic Block",
        description: "Este es nuestro primer bloque",
        icon: "smiley",
        category: "layout",
        attributes: {
            content: {
                type: 'string', // Definimos el tipo de dato que debe esperar el componente. En este caso es un texto, por eso lo declaramos como string.
                default: 'Hello world' // Definimos el valor por defecto.
            },
            mediaURL: {
                type: 'string' // Definimos el tipo de dato que debe esperar el componente. En este caso es un texto (URL de la imagen), por eso lo declaramos como string.
            },
            mediaAlt: {
                type: 'string' // Definimos el tipo de dato que debe esperar el componente. En este caso es un texto (Texto Alternativo de la imagen), por eso lo declaramos como string.
            }
        },
        edit: (props) => {
            const { attributes: {content}, setAttributes, className, isSelected} = props;
        
            // Función para guardar el atributo content
            const handlerOnChangeInput = (newContent) => {
                setAttributes( {content: newContent})
            }
        
            // Función para guardar el atributo mediaURL y mediaAlt
            const handlerOnSelectMediaUpload = (image) => {
                setAttributes({
                    mediaURL: image.sizes.full.url,
                    mediaAlt: image.alt
                })
            }
        
            return <>
                <InspectorControls>
                    <PanelBody // Primer panel en la sidebar
                        title="Modificar texto del Bloque Básico"
                        initialOpen={ true }
                    >
                        <PanelRow>
                            <TextControl
                                label="Complete el campo" // Indicaciones del campo
                                value={ content } // Asignación del atributo correspondiente
                                onChange={ handlerOnChangeInput } // Asignación de función para gestionar el evento OnChange
                            />
                        </PanelRow>
                    </PanelBody>
                    <PanelBody // Segundo panel en la sidebar
                        title="Seleccioná una imagen"
                        initialOpen={ true }
                    >
                        <PanelRow>
                            <MediaUpload 
                                onSelect={ handlerOnSelectMediaUpload } // Asignación de función para gestionar el evento OnSelect
                                type="image" // Limita los tipos de archivos que se pueden seleccionar
                                // Se envía el evento open y el renderizado del elemento que desencadenará la apertura de la librería, en este caso un botón
                                render={ ({open}) => {
                                    //Agregamos las clases de los botones de WordPress habituales para que mantenga el estilo dentro del editor
                                    return <Button className="components-icon-button image-block-btn is-button is-default is-large" onClick={open}>Elegir una imagen</Button>;
                                }}
                            />
                        </PanelRow>
                    </PanelBody>
                </InspectorControls>
                <ServerSideRender // Renderizado de bloque dinámico
                    block="pg/basic" // Nombre del bloque
                    attributes={ props.attributes } // Se envían todos los atributos
                />
            </>
        },
        save: () => null
    }
)