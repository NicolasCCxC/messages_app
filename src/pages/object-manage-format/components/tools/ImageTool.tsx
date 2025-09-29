import React, { useContext, useState } from 'react';
import { ChangeEvent } from '@models/Input';
import { Icon } from '@components/icon';
import { NotificationType, Toast } from '@components/toast';
import { ManageObjectContext } from '@pages/object-manage-format/context';
import { IMAGE_SIZE_ERROR_MESSAGE } from '@constants/Validation.ts';
import { SizeControls } from './SizeControls';
import { MAX_SIZE } from '.';

export const ImageTool: React.FC = () => {
    const { updateElementProperties } = useContext(ManageObjectContext);

    const [showToast, setShowToast] = useState(false);

    const handleImageUpload = (event: ChangeEvent): void => {
        setShowToast(false)
        const file = event.target.files?.[0];
        if (!file || file.size > MAX_SIZE) {
            setShowToast(true)
            return
        }

        const reader = new FileReader();
        reader.onloadend = (): void => {
            const base64String = reader.result as string;
            updateElementProperties('image', base64String);
        };
        reader.readAsDataURL(file);
    };

    const toggleToast = (): void => setShowToast(!showToast);

    return (
        <>
            <div>
                <p className="mb-1 text-sm text-black">Imagen</p>
                <label htmlFor="uploadImage">
                    <div className="cursor-pointer w-[12.25rem] h-[3.625rem] bg-white rounded flex flex-col justify-center items-center border border-gray-dark">
                        <Icon name="upload" />
                        <p className="text-xs text-gray-dark">Subir imagen</p>
                        <input
                            id="uploadImage"
                            className="hidden"
                            type="file"
                            accept="image/png, image/jpeg, image/jpg"
                            onChange={handleImageUpload}
                        />
                    </div>
                </label>
            </div>
            <Toast
                message={IMAGE_SIZE_ERROR_MESSAGE}
                open={showToast}
                onClose={toggleToast}
                type={NotificationType.Error}
            />
            <SizeControls />
        </>
    );
};
