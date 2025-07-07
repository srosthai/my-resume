'use strict';

const vue = require('vue');
const shared_renderSlotFragments = require('../shared/renderSlotFragments.cjs');

const Slot = vue.defineComponent({
  name: "PrimitiveSlot",
  inheritAttrs: false,
  setup(_, { attrs, slots }) {
    return () => {
      if (!slots.default)
        return null;
      const children = shared_renderSlotFragments.renderSlotFragments(slots.default());
      const firstNonCommentChildrenIndex = children.findIndex((child) => child.type !== vue.Comment);
      if (firstNonCommentChildrenIndex === -1)
        return children;
      const firstNonCommentChildren = children[firstNonCommentChildrenIndex];
      delete firstNonCommentChildren.props?.ref;
      const mergedProps = firstNonCommentChildren.props ? vue.mergeProps(attrs, firstNonCommentChildren.props) : attrs;
      const cloned = vue.cloneVNode({ ...firstNonCommentChildren, props: {} }, mergedProps);
      if (children.length === 1)
        return cloned;
      children[firstNonCommentChildrenIndex] = cloned;
      return children;
    };
  }
});

exports.Slot = Slot;
//# sourceMappingURL=Slot.cjs.map
