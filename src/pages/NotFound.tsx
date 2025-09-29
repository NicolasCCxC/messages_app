import React from 'react';
import { useRouteError, isRouteErrorResponse } from 'react-router-dom';

const NotFound: React.FC = () => {
    const error = useRouteError();

    if (isRouteErrorResponse(error)) {
        return (
            <div className="flex items-center justify-center min-h-screen bg-gray-100">
                <div className="p-8 bg-white rounded-lg shadow-md">
                    <h1 className="mb-4 text-4xl font-bold text-red-600">Oops!</h1>
                    <p className="mb-4 text-xl">Sorry, an unexpected error has occurred.</p>
                    <p className="text-gray-600">
                        <i>{error.statusText || error.data?.message}</i>
                    </p>
                </div>
            </div>
        );
    } else {
        return <div>Oops</div>;
    }
};

export default NotFound;
