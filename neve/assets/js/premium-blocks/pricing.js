/**
 * Pricing Table Block for Neve Theme
 */

const { registerBlockType } = wp.blocks;
const { InspectorControls, RichText } = wp.blockEditor;
const { PanelBody, RangeControl, ToggleControl, TextControl, IconButton } = wp.components;
const { Fragment } = wp.element;

registerBlockType('neve/pricing-table', {
    title: 'Neve Pricing Table',
    icon: 'money-alt',
    category: 'neve-blocks',
    attributes: {
        plans: {
            type: 'array',
            default: [{
                title: 'Basic Plan',
                price: '29',
                currency: '$',
                period: '/month',
                features: ['Feature 1', 'Feature 2', 'Feature 3'],
                button_text: 'Get Started',
                button_url: '#',
                featured: false
            }]
        },
        columns: {
            type: 'number',
            default: 3
        }
    },

    edit: function(props) {
        const { attributes, setAttributes } = props;
        const { plans, columns } = attributes;

        const addPlan = () => {
            const newPlans = [...plans, {
                title: 'New Plan',
                price: '0',
                currency: '$',
                period: '/month',
                features: ['Feature 1'],
                button_text: 'Get Started',
                button_url: '#',
                featured: false
            }];
            setAttributes({ plans: newPlans });
        };

        const updatePlan = (index, field, value) => {
            const newPlans = [...plans];
            newPlans[index][field] = value;
            setAttributes({ plans: newPlans });
        };

        const removePlan = (index) => {
            const newPlans = plans.filter((_, i) => i !== index);
            setAttributes({ plans: newPlans });
        };

        const addFeature = (planIndex) => {
            const newPlans = [...plans];
            newPlans[planIndex].features.push('New Feature');
            setAttributes({ plans: newPlans });
        };

        const updateFeature = (planIndex, featureIndex, value) => {
            const newPlans = [...plans];
            newPlans[planIndex].features[featureIndex] = value;
            setAttributes({ plans: newPlans });
        };

        const removeFeature = (planIndex, featureIndex) => {
            const newPlans = [...plans];
            newPlans[planIndex].features.splice(featureIndex, 1);
            setAttributes({ plans: newPlans });
        };

        return (
            <Fragment>
                <InspectorControls>
                    <PanelBody title="Layout Settings">
                        <RangeControl
                            label="Columns"
                            value={columns}
                            onChange={(value) => setAttributes({ columns: value })}
                            min={1}
                            max={4}
                        />
                    </PanelBody>
                </InspectorControls>

                <div className={`neve-pricing-block columns-${columns}`}>
                    <h3>Pricing Table</h3>
                    {plans.map((plan, planIndex) => (
                        <div key={planIndex} className={`pricing-plan ${plan.featured ? 'featured' : ''}`}>
                            <div className="plan-header">
                                <RichText
                                    tagName="h4"
                                    placeholder="Plan Title"
                                    value={plan.title}
                                    onChange={(value) => updatePlan(planIndex, 'title', value)}
                                />
                                <div className="plan-price">
                                    <TextControl
                                        placeholder="$"
                                        value={plan.currency}
                                        onChange={(value) => updatePlan(planIndex, 'currency', value)}
                                    />
                                    <TextControl
                                        placeholder="29"
                                        value={plan.price}
                                        onChange={(value) => updatePlan(planIndex, 'price', value)}
                                    />
                                    <TextControl
                                        placeholder="/month"
                                        value={plan.period}
                                        onChange={(value) => updatePlan(planIndex, 'period', value)}
                                    />
                                </div>
                                <ToggleControl
                                    label="Featured Plan"
                                    checked={plan.featured}
                                    onChange={(value) => updatePlan(planIndex, 'featured', value)}
                                />
                            </div>
                            
                            <div className="plan-features">
                                <h5>Features:</h5>
                                {plan.features.map((feature, featureIndex) => (
                                    <div key={featureIndex} className="feature-item">
                                        <TextControl
                                            value={feature}
                                            onChange={(value) => updateFeature(planIndex, featureIndex, value)}
                                        />
                                        <IconButton
                                            icon="trash"
                                            onClick={() => removeFeature(planIndex, featureIndex)}
                                        />
                                    </div>
                                ))}
                                <IconButton
                                    icon="plus"
                                    onClick={() => addFeature(planIndex)}
                                >
                                    Add Feature
                                </IconButton>
                            </div>

                            <div className="plan-footer">
                                <TextControl
                                    placeholder="Button Text"
                                    value={plan.button_text}
                                    onChange={(value) => updatePlan(planIndex, 'button_text', value)}
                                />
                                <TextControl
                                    placeholder="Button URL"
                                    value={plan.button_url}
                                    onChange={(value) => updatePlan(planIndex, 'button_url', value)}
                                />
                            </div>

                            <IconButton
                                icon="trash"
                                label="Remove plan"
                                onClick={() => removePlan(planIndex)}
                            />
                        </div>
                    ))}
                    <IconButton isPrimary onClick={addPlan}>
                        Add Plan
                    </IconButton>
                </div>
            </Fragment>
        );
    },

    save: function() {
        return null; // Server-side rendering
    }
});