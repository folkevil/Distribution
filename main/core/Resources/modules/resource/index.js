import get from 'lodash/get'

import {constants} from '#/main/core/tool/constants'

import {ResourceMain} from '#/main/core/resource/containers/main'
import {reducer} from '#/main/core/resource/store'

export const App = () => ({
  component: ResourceMain,
  store: reducer,
  initialData: (initialData) => Object.assign({}, initialData, {
    tool: {
      name: 'resource_manager',
      currentContext: {
        type: get(initialData, 'resourceNode.workspace') ? constants.TOOL_WORKSPACE : constants.TOOL_DESKTOP,
        data: get(initialData, 'resourceNode.workspace')
      }
    }
  })
})