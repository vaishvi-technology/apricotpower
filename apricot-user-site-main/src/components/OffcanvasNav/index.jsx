/* eslint-disable react/prop-types */
import { Offcanvas } from 'react-bootstrap';

const OffcanvasNav = ({ show, handleClose, title, children }) => {
  return (
    <>
      <Offcanvas show={show} className="offcanvasMenu" onHide={handleClose} placement="start">
        <Offcanvas.Header closeButton>
          <Offcanvas.Title>{title}</Offcanvas.Title>
        </Offcanvas.Header>
        <Offcanvas.Body>
          {/* Display content passed via props.children */}
          {children}
        </Offcanvas.Body>
      </Offcanvas>
    </>
  );
};

export default OffcanvasNav;
