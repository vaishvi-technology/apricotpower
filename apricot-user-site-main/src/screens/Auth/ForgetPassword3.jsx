import { useState, useEffect } from 'react';
import { useNavigate } from 'react-router-dom';



import { AuthLayout } from '../../components/Layout/AuthLayout';
import CustomInput from "../../components/CustomInput"
import CustomButton from '../../components/CustomButton';
import CustomModal from '../../components/CustomModal';

import "./style.css";
import { toast } from 'react-toastify';

const ForgetPassword3 = () => {
    const [formData, setFormData] = useState({})
    const navigate = useNavigate()

    const [showModal, setShowModal] = useState(false);

    useEffect(() => {
        document.title = 'Apricot Power| Password Recovery';
    }, [])


    const handleClick = () => {
        setShowModal(true)
    }

    const redirectHome = () => {
        navigate('/dashboard')
    }
// ${process.env.REACT_APP_API_URL}/public/api/reset_password




const handleSubmit = async (event) => {
    event.preventDefault();
    
    const formDataMethod = new FormData();
    const email = localStorage.getItem('email');
    const otp = localStorage.getItem('otp');

    formDataMethod.append('email', email);
    formDataMethod.append('otp', otp);
    formDataMethod.append('password', formData.password);
    formDataMethod.append('password_confirmation', formData.password_confirmation);

    document.querySelector('.loaderBox').classList.remove("d-none");

    const apiUrl = `${process.env.REACT_APP_API_URL}/public/api/reset_password`;


    try {
        const response = await fetch(apiUrl, {
            method: 'POST',
            body: formDataMethod
        });

        if (response.ok) {


            document.querySelector('.loaderBox').classList.add("d-none");
            navigate('/login')
            
        } else {
            document.querySelector('.loaderBox').classList.add("d-none");
            // alert('Invalid Credentials')
            const responseData = await response.json();
            toast.error(responseData?.message);

            console.error('Login failed');
        }
    } catch (error) {
        document.querySelector('.loaderBox').classList.add("d-none");
        console.error('Error:', error);
    }
};

    return (
        <>
            <AuthLayout authTitle='Password Recovery' authPara='Enter a new password.' backOption={true}>
                <form onSubmit={handleSubmit}>
                    <CustomInput label='New Password'
                    onChange={(event) => {
                        setFormData({ ...formData, password: event.target.value })
                    }}
                    
                    required id='pass' type='password' placeholder='Enter New Password' labelClass='mainLabel' inputClass='mainInput' />
                    <CustomInput label='Confirm Password' required id='cPass' type='password' placeholder='Confirm Password' labelClass='mainLabel' inputClass='mainInput' 
                    
                    onChange={(event) => {
                        setFormData({ ...formData, password_confirmation: event.target.value })
                    }}
                    />

                    <div className="mt-4 text-center">
                        <CustomButton type='submit' variant='primaryButton' text='Update'  />
                    </div>
                </form>
            </AuthLayout>

            <CustomModal show={showModal} success heading='Password updated successfully. Please login to continue' close={redirectHome} btnTxt="Continue"/>
        </>
    )
}



export default ForgetPassword3





