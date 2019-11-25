import React, { Component } from 'react';
import { NavLink, withRouter } from 'react-router-dom';
import Navbar from 'react-bulma-components/lib/components/navbar';

const Nav = function(props) {
    const colors = {
        Default: '',
        primary: 'primary',
        info: 'info',
        danger: 'danger',
        warning: 'warning',
        success: 'success',
        white: 'white',
        black: 'black',
        light: 'light',
        dark: 'dark',
        link: 'link',
    };
    return (
        <Navbar
            color={colors.primary}
            fixed={'top'}
            active={false}
            transparent={false}
        >
            <Navbar.Brand>
                <Navbar.Item renderAs="div">
                    <NavLink to={'/'}>
                        <img
                            src="/images/logo.svg"
                            alt="Bobby.af is the global leader in what i feel like doing."
                            className="logo"
                        />
                    </NavLink>
                </Navbar.Item>
            </Navbar.Brand>
            <Navbar.Menu active={''}>
                <Navbar.Container>
                    <Navbar.Item renderAs="div">
                        <NavLink to={'/submissions'}>Submissions</NavLink>
                    </Navbar.Item>
                    <Navbar.Item renderAs="div">
                        <NavLink to={'/config'}>Panels</NavLink>
                    </Navbar.Item>
                </Navbar.Container>
                <Navbar.Container position="end">
                    <Navbar.Item href="/user">User</Navbar.Item>
                </Navbar.Container>
            </Navbar.Menu>
        </Navbar>
    );
};
export default Nav;
