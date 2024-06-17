import {
    StyleSheet,
    View,
} from "react-native";
import React from "react";
//import PasswordRecovery from "../crystalgate/components/password-recovery";
import Verification from "../crystalgate/components/verification"; 

export default function App() {
    return (
        <View style={styles.container}>
            <Verification/>
        </View>
    );
}

const styles = StyleSheet.create({
    container: {
        flex: 1,
    },
    
});
