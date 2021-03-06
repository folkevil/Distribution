import React from 'react'
import {PropTypes as T} from 'prop-types'

import {trans} from '#/main/app/intl/translation'
import {FormData} from '#/main/app/content/form/containers/data'

const WindowsLiveParameters = props =>
  <FormData
    name={props.name+'.windows_live'}
    sections={[
      {
        title: trans('general'),
        primary: true,
        fields: [

        ]
      }
    ]}
  />

WindowsLiveParameters.propTypes = {
  name: T.string.isRequired
}

export {
  WindowsLiveParameters
}