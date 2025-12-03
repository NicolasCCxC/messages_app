package avvillas.core.web.configuration.security;

import avvillas.core.web.configuration.security.filter.InjectionFilter;
import org.springframework.boot.web.servlet.FilterRegistrationBean;
import org.springframework.context.annotation.Bean;
import org.springframework.context.annotation.Configuration;

@Configuration
public class FilterConfig {

    @Bean
    public FilterRegistrationBean<InjectionFilter> injectionFilterRegistration() {
        FilterRegistrationBean<InjectionFilter> registrationBean = new FilterRegistrationBean<>();
        registrationBean.setFilter(new InjectionFilter());
        registrationBean.addUrlPatterns("/*");
        registrationBean.setOrder(1);
        return registrationBean;
    }
}