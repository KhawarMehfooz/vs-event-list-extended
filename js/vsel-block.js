"use strict";

(function () {
	var _wp = wp,
	registerBlockType = _wp.blocks.registerBlockType,
	createElement = _wp.element.createElement,
	serverSideRender = _wp.serverSideRender === void 0 ? _wp.components.serverSideRender : _wp.serverSideRender,
	InspectorControls = _wp.blockEditor.InspectorControls,
	PanelBody = _wp.components.PanelBody,
	SelectControl = _wp.components.SelectControl,
	TextareaControl = _wp.components.TextareaControl,
	Placeholder = _wp.components.Placeholder,
	Button = _wp.components.Button;

	registerBlockType('vsel/vsel-block', {
		title: 'VS Event List',
		icon: 'calendar',
		category: 'text',
		attributes: {
			listType: {
				type: 'string'
			},
			shortcodeSettings: {
				type: 'string'
			},
			noNewChanges: {
				type: 'boolean'
			},
			executed: {
				type: 'boolean'
			}
		},
		edit: function edit(props) {
			var _props = props,
			setAttributes = _props.setAttributes,
			attributes = _props.attributes,
			attributes$lis = attributes.listType,
			listType = attributes$lis === void 0 ? null : attributes$lis,
			listOptions = vsel_block_l10n.listTypes.map( value => (
				{ value: value.id, label: value.label }
			) ),
			attributes$sho = attributes.shortcodeSettings,
			shortcodeSettings = attributes$sho === void 0 ? null : attributes$sho,
			attributes$cli = attributes.noNewChanges,
			noNewChanges = attributes$cli === void 0 ? true : attributes$cli,
			attributes$exe = attributes.executed,
			executed = attributes$exe === void 0 ? false : attributes$exe;

			function selectType(value) {
				setAttributes({
					listType: value
				});
			}

			function setState(shortcodeSettingsContent) {
				setAttributes({
					noNewChanges: false,
					shortcodeSettings: shortcodeSettingsContent
				});
			}

			function previewClick(content) {
				setAttributes({
					noNewChanges: true,
					executed: false
				});
			}

			function afterRender() {
				setAttributes({
					executed: true
				});
			}

			var jsx;

			jsx = [React.createElement(InspectorControls, {
					key: "vsel-block-editor-inspector-controls"
				},
				React.createElement(PanelBody, {
					key: "vsel-block-editor-panel-body",
					title: vsel_block_l10n.addSettings
				},
				React.createElement(SelectControl, {
					key: "vsel-block-editor-select",
					label: vsel_block_l10n.listTypeLabel,
					value: listType,
					options: listOptions,
					onChange: selectType
				}),
				React.createElement(TextareaControl, {
					key: "vsel-block-editor-textarea",
					label: vsel_block_l10n.shortcodeSettingsLabel,
					help: vsel_block_l10n.example + ": posts_per_page=\"5\"",
					value: shortcodeSettings,
					onChange: setState
				}),
				React.createElement('div', {
					key: "vsel-block-editor-preview-button-div",
					className: "components-base-control"
				},
				React.createElement(Button, {
					key: "vsel-block-editor-preview-button-primary",
					onClick: previewClick,
					isSecondary: true
				}, vsel_block_l10n.previewButton
				)
				),
				React.createElement('div', {
					key: "vsel-block-editor-info-div",
					className: "components-base-control"
				}, vsel_block_l10n.linkText + " "
				,
				React.createElement('a', {
					key: "vsel-block-editor-info-link",
					href: "https://wordpress.org/plugins/very-simple-event-list",
					rel: "noopener noreferrer",
					target: "_blank"
				}, vsel_block_l10n.linkLabel
				)
				)
				)
			)];

			if (noNewChanges) {
				afterRender();
				jsx.push(React.createElement(serverSideRender, {
					key: "vsel-block-editor-server-side-render",
					block: "vsel/vsel-block",
					attributes: props.attributes
				}));
			} else {
				props.attributes.noNewChanges = false;
				jsx.push(React.createElement(Placeholder, {
					key: "vsel-block-editor-placeholder"
				}, React.createElement(Button, {
					key: "vsel-block-editor-preview-button-secondary",
					onClick: previewClick,
					isSecondary: true
				}, vsel_block_l10n.previewButton
				)
				));
			}

			return jsx;
		},
		save: function save() {
			return null;
		}
	});
})();
