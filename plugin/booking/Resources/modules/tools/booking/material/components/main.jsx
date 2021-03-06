import React from 'react'
import {PropTypes as T} from 'prop-types'

import {Routes} from '#/main/app/router'

import {MaterialList} from '#/plugin/booking/tools/booking/material/containers/list'
import {MaterialDetails} from '#/plugin/booking/tools/booking/material/containers/details'

const MaterialMain = (props) =>
  <Routes
    path={props.path+'/materials'}
    routes={[
      {
        path: '/',
        exact: true,
        component: MaterialList
      }, {
        path: '/:id',
        onEnter: (params = {}) => props.open(params.id),
        component: MaterialDetails
      }
    ]}
  />

MaterialMain.propTypes = {
  path: T.string.isRequired,
  open: T.func.isRequired
}

export {
  MaterialMain
}
