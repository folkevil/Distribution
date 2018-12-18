/* eslint-disable */

import {registry} from '#/main/app/plugins/registry'

registry.add('HeVinciCompetencyBundle', {
  data: {
    types: {
      'competency_scale' : () => { return import(/* webpackChunkName: "plugin-competency-data-scale" */   '#/plugin/competency/data/types/scale') },
      'ability'          : () => { return import(/* webpackChunkName: "plugin-competency-data-ability" */ '#/plugin/competency/data/types/ability') }
    },
  },
  actions: {
    resource: {
      'manage_competencies': () => { return import(/* webpackChunkName: "competency-action-resource-manage-competencies" */ '#/plugin/competency/resource/actions/manage-competencies') }
    }
  }
})
