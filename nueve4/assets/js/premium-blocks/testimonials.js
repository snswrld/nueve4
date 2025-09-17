/**
 * Testimonials Block for Nueve4 Theme
 */

const { registerBlockType } = wp.blocks;
const { InspectorControls, MediaUpload, RichText } = wp.blockEditor;
const { PanelBody, Button, SelectControl, IconButton } = wp.components;
const { Fragment } = wp.element;

registerBlockType('nueve4/testimonials', {
    title: 'Nueve4 Testimonials',
    icon: 'format-quote',
    category: 'nueve4-blocks',
    attributes: {
        testimonials: {
            type: 'array',
            default: [{
                content: 'This is an amazing product!',
                name: 'John Doe',
                position: 'CEO, Company',
                image: ''
            }]
        },
        layout: {
            type: 'string',
            default: 'grid'
        }
    },

    edit: function(props) {
        const { attributes, setAttributes } = props;
        const { testimonials, layout } = attributes;

        const addTestimonial = () => {
            const newTestimonials = [...testimonials, {
                content: 'New testimonial content',
                name: 'Name',
                position: 'Position',
                image: ''
            }];
            setAttributes({ testimonials: newTestimonials });
        };

        const updateTestimonial = (index, field, value) => {
            const newTestimonials = [...testimonials];
            newTestimonials[index][field] = value;
            setAttributes({ testimonials: newTestimonials });
        };

        const removeTestimonial = (index) => {
            const newTestimonials = testimonials.filter((_, i) => i !== index);
            setAttributes({ testimonials: newTestimonials });
        };

        return (
            <Fragment>
                <InspectorControls>
                    <PanelBody title="Layout Settings">
                        <SelectControl
                            label="Layout"
                            value={layout}
                            options={[
                                { label: 'Grid', value: 'grid' },
                                { label: 'Slider', value: 'slider' },
                                { label: 'List', value: 'list' }
                            ]}
                            onChange={(value) => setAttributes({ layout: value })}
                        />
                    </PanelBody>
                </InspectorControls>

                <div className={`nueve4-testimonials-block layout-${layout}`}>
                    <h3>Testimonials</h3>
                    {testimonials.map((testimonial, index) => (
                        <div key={index} className="testimonial-item">
                            <RichText
                                tagName="p"
                                placeholder="Testimonial content..."
                                value={testimonial.content}
                                onChange={(value) => updateTestimonial(index, 'content', value)}
                            />
                            <div className="testimonial-author">
                                <MediaUpload
                                    onSelect={(media) => updateTestimonial(index, 'image', media.url)}
                                    type="image"
                                    render={({ open }) => (
                                        <Button onClick={open} className="button">
                                            {testimonial.image ? 'Change Image' : 'Select Image'}
                                        </Button>
                                    )}
                                />
                                <RichText
                                    tagName="h4"
                                    placeholder="Name"
                                    value={testimonial.name}
                                    onChange={(value) => updateTestimonial(index, 'name', value)}
                                />
                                <RichText
                                    tagName="span"
                                    placeholder="Position"
                                    value={testimonial.position}
                                    onChange={(value) => updateTestimonial(index, 'position', value)}
                                />
                            </div>
                            <IconButton
                                icon="trash"
                                label="Remove testimonial"
                                onClick={() => removeTestimonial(index)}
                            />
                        </div>
                    ))}
                    <Button isPrimary onClick={addTestimonial}>
                        Add Testimonial
                    </Button>
                </div>
            </Fragment>
        );
    },

    save: function() {
        return null; // Server-side rendering
    }
});