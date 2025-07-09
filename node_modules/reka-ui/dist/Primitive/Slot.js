import { defineComponent, Comment, mergeProps, cloneVNode } from 'vue';
import { r as renderSlotFragments } from '../shared/renderSlotFragments.js';

const Slot = defineComponent({
  name: "PrimitiveSlot",
  inheritAttrs: false,
  setup(_, { attrs, slots }) {
    return () => {
      if (!slots.default)
        return null;
      const children = renderSlotFragments(slots.default());
      const firstNonCommentChildrenIndex = children.findIndex((child) => child.type !== Comment);
      if (firstNonCommentChildrenIndex === -1)
        return children;
      const firstNonCommentChildren = children[firstNonCommentChildrenIndex];
      delete firstNonCommentChildren.props?.ref;
      const mergedProps = firstNonCommentChildren.props ? mergeProps(attrs, firstNonCommentChildren.props) : attrs;
      const cloned = cloneVNode({ ...firstNonCommentChildren, props: {} }, mergedProps);
      if (children.length === 1)
        return cloned;
      children[firstNonCommentChildrenIndex] = cloned;
      return children;
    };
  }
});

export { Slot as S };
//# sourceMappingURL=Slot.js.map
