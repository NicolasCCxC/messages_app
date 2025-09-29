import { FormEvent, useEffect, useState } from 'react';
import { Button } from '@components/button';
import { TextInput } from '@components/text-input';
import { ChangeEvent } from '@models/Input';
import { login } from '@redux/auth/actions';
import { cleanError } from '@redux/auth/authSlice';
import { useAppDispatch, useAppSelector } from '@redux/store';
import { validatePattern } from '@utils/Input';
import { FieldLength, USER_REGEX } from '.';
import './Login.scss';

const Login: React.FC = () => {
    const dispatch = useAppDispatch();
    const { error } = useAppSelector(state => state.auth);

    const [data, setData] = useState<{ email: string; password: string }>({ email: '', password: '' });
    const [validate, setValidate] = useState(false);
    const { email, password } = data;

    useEffect(() => {
        dispatch(cleanError());
    }, [dispatch]);

    const closeWindow = (): void => window.close();

    const handleSubmit = async (e: FormEvent): Promise<void> => {
        e.preventDefault();
        if (!(email && password)) return setValidate(true);
        await dispatch(login(data));
    };

    const handleValueChange = ({ target: { name, value } }: ChangeEvent): void => {
        if (validatePattern(value, USER_REGEX)) setData({ ...data, [name]: value });
    };

    return (
        <div className="login">
            <div className="login__background" />
            <form className="login__form">
                <TextInput
                    placeholder="Usuario"
                    onChange={handleValueChange}
                    value={data.email}
                    inputClassName="h-12"
                    maxLength={FieldLength.User}
                    error={validate && !data.email}
                    name="email"
                />
                <TextInput
                    placeholder="Contraseña"
                    onChange={handleValueChange}
                    value={data.password}
                    wrapperClassName="my-7"
                    inputClassName="w-max h-12"
                    maxLength={FieldLength.Password}
                    error={validate && !data.password}
                    name="password"
                    type="password"
                />
                <div className="flex justify-center gap-7">
                    <Button text="Cancelar" onClick={closeWindow} />
                    <Button text="Iniciar sesión" type="submit" onClick={handleSubmit} />
                </div>
                {error && <p className="login__error">{error}</p>}
            </form>
        </div>
    );
};

export default Login;
