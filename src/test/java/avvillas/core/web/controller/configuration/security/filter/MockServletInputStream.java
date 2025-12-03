package avvillas.core.web.controller.configuration.security.filter;

import jakarta.servlet.ReadListener;
import jakarta.servlet.ServletInputStream;

import java.io.InputStream;

public class MockServletInputStream extends ServletInputStream {
    private final InputStream inputStream;

    public MockServletInputStream(InputStream inputStream) {
        this.inputStream = inputStream;
    }

    @Override
    public boolean isFinished() {
        return false;
    }

    @Override
    public boolean isReady() {
        return true;
    }

    @Override
    public void setReadListener(ReadListener readListener) {
        throw new UnsupportedOperationException("setReadListener not supported");
    }

    @Override
    public int read() {
        try {
            return inputStream.read();
        } catch (Exception e) {
            throw new RuntimeException("Error al leer el flujo de entrada", e);
        }
    }
}