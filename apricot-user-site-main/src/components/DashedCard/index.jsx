import React from 'react'

const DashedCard = (props) => {
  return (
    <div className={`dashed-card ${props.className} ${props.variant}`}>
        {props.children}
    </div>
  )
}

export default DashedCard
