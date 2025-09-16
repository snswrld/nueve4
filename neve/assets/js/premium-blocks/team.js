/**
 * Team Members Block for Neve Theme
 */

const { registerBlockType } = wp.blocks;
const { InspectorControls, MediaUpload, RichText } = wp.blockEditor;
const { PanelBody, Button, RangeControl, TextControl, IconButton } = wp.components;
const { Fragment } = wp.element;

registerBlockType('nueve4/team-members', {
    title: 'Neve Team Members',
    icon: 'groups',
    category: 'nueve4-blocks',
    attributes: {
        members: {
            type: 'array',
            default: [{
                name: 'John Doe',
                position: 'CEO',
                bio: 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
                image: '',
                social: []
            }]
        },
        columns: {
            type: 'number',
            default: 3
        }
    },

    edit: function(props) {
        const { attributes, setAttributes } = props;
        const { members, columns } = attributes;

        const addMember = () => {
            const newMembers = [...members, {
                name: 'New Member',
                position: 'Position',
                bio: 'Bio description',
                image: '',
                social: []
            }];
            setAttributes({ members: newMembers });
        };

        const updateMember = (index, field, value) => {
            const newMembers = [...members];
            newMembers[index][field] = value;
            setAttributes({ members: newMembers });
        };

        const removeMember = (index) => {
            const newMembers = members.filter((_, i) => i !== index);
            setAttributes({ members: newMembers });
        };

        const addSocialLink = (memberIndex) => {
            const newMembers = [...members];
            newMembers[memberIndex].social.push({
                platform: 'facebook',
                url: '#',
                icon: 'fab fa-facebook'
            });
            setAttributes({ members: newMembers });
        };

        const updateSocialLink = (memberIndex, socialIndex, field, value) => {
            const newMembers = [...members];
            newMembers[memberIndex].social[socialIndex][field] = value;
            setAttributes({ members: newMembers });
        };

        const removeSocialLink = (memberIndex, socialIndex) => {
            const newMembers = [...members];
            newMembers[memberIndex].social.splice(socialIndex, 1);
            setAttributes({ members: newMembers });
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

                <div className={`nueve4-team-block columns-${columns}`}>
                    <h3>Team Members</h3>
                    {members.map((member, memberIndex) => (
                        <div key={memberIndex} className="team-member">
                            <div className="member-image">
                                <MediaUpload
                                    onSelect={(media) => updateMember(memberIndex, 'image', media.url)}
                                    type="image"
                                    render={({ open }) => (
                                        <Button onClick={open} className="button">
                                            {member.image ? (
                                                <img src={member.image} alt={member.name} style={{maxWidth: '100px'}} />
                                            ) : (
                                                'Select Image'
                                            )}
                                        </Button>
                                    )}
                                />
                            </div>
                            
                            <div className="member-info">
                                <RichText
                                    tagName="h4"
                                    placeholder="Member Name"
                                    value={member.name}
                                    onChange={(value) => updateMember(memberIndex, 'name', value)}
                                />
                                <RichText
                                    tagName="span"
                                    placeholder="Position"
                                    value={member.position}
                                    onChange={(value) => updateMember(memberIndex, 'position', value)}
                                />
                                <RichText
                                    tagName="p"
                                    placeholder="Bio description..."
                                    value={member.bio}
                                    onChange={(value) => updateMember(memberIndex, 'bio', value)}
                                />
                                
                                <div className="social-links">
                                    <h5>Social Links:</h5>
                                    {member.social.map((social, socialIndex) => (
                                        <div key={socialIndex} className="social-item">
                                            <TextControl
                                                placeholder="Platform (facebook, twitter, etc.)"
                                                value={social.platform}
                                                onChange={(value) => {
                                                    updateSocialLink(memberIndex, socialIndex, 'platform', value);
                                                    updateSocialLink(memberIndex, socialIndex, 'icon', `fab fa-${value}`);
                                                }}
                                            />
                                            <TextControl
                                                placeholder="URL"
                                                value={social.url}
                                                onChange={(value) => updateSocialLink(memberIndex, socialIndex, 'url', value)}
                                            />
                                            <IconButton
                                                icon="trash"
                                                onClick={() => removeSocialLink(memberIndex, socialIndex)}
                                            />
                                        </div>
                                    ))}
                                    <IconButton
                                        icon="plus"
                                        onClick={() => addSocialLink(memberIndex)}
                                    >
                                        Add Social Link
                                    </IconButton>
                                </div>
                            </div>

                            <IconButton
                                icon="trash"
                                label="Remove member"
                                onClick={() => removeMember(memberIndex)}
                            />
                        </div>
                    ))}
                    <Button isPrimary onClick={addMember}>
                        Add Team Member
                    </Button>
                </div>
            </Fragment>
        );
    },

    save: function() {
        return null; // Server-side rendering
    }
});