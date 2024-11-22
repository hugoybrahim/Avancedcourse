import { registerBlockType } from '@wordpress/blocks';

registerBlockType(
    'pg/basic',
    {
        title: "Basic Block",
        description: "Este es nuestro primer bloque",
        icon: "smiley",
        category: "layout",
        edit: (props) => {
            const
        },
        save: () => <h2>Hello WOrl</h2>
    }
)