import { Button } from '@components/button';
import { Modal } from '@components/modal';

export const ModalView: React.FC<{ toggleModal: () => void }> = ({ toggleModal }) => {
    return (
        <Modal noButtons open onClose={toggleModal} title="Visualizar" modalClassName="w-[30.375rem] h-[23.625rem]">
            <div className="w-[400px] h-[12.5rem] bg-gray-light my-7 mx-auto">preview</div>
            <Button buttonClassName="block mx-auto" color="secondary" onClick={toggleModal} text="Cerrar" />
        </Modal>
    );
};
