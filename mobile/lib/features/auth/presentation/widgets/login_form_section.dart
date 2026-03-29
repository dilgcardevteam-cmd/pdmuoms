import 'package:flutter/material.dart';

class LoginFormSection extends StatelessWidget {
  const LoginFormSection({super.key});

  @override
  Widget build(BuildContext context) {
    final theme = Theme.of(context);

    return Container(
      padding: const EdgeInsets.fromLTRB(24, 28, 24, 30),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(30),
        boxShadow: const [
          BoxShadow(
            color: Color(0x22000000),
            blurRadius: 28,
            offset: Offset(0, 12),
          ),
        ],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.stretch,
        children: [
          Text(
            'Sign in',
            style: theme.textTheme.headlineMedium?.copyWith(
              color: const Color(0xFF3F3B3E),
              fontWeight: FontWeight.w700,
              fontSize: 34,
            ),
          ),
          const SizedBox(height: 8),
          Container(
            width: 58,
            height: 4,
            decoration: BoxDecoration(
              color: const Color(0xFF2E6FDB),
              borderRadius: BorderRadius.circular(999),
            ),
          ),
          const SizedBox(height: 28),
          Text(
            'Username',
            style: theme.textTheme.labelLarge?.copyWith(
              color: const Color(0xFF7C7A7D),
              fontWeight: FontWeight.w700,
            ),
          ),
          const SizedBox(height: 10),
          const _LoginTextField(
            hintText: 'Enter username',
            prefixIcon: Icons.person_outline_rounded,
            textInputAction: TextInputAction.next,
          ),
          const SizedBox(height: 22),
          Text(
            'Password',
            style: theme.textTheme.labelLarge?.copyWith(
              color: const Color(0xFF7C7A7D),
              fontWeight: FontWeight.w700,
            ),
          ),
          const SizedBox(height: 10),
          const _LoginTextField(
            hintText: 'Enter password',
            prefixIcon: Icons.lock_outline_rounded,
            obscureText: true,
          ),
          const SizedBox(height: 16),
          Row(
            children: [
              Icon(
                Icons.check_box_outline_blank_rounded,
                size: 18,
                color: const Color(0xFF2E6FDB),
              ),
              const SizedBox(width: 8),
              Text(
                'Remember Me',
                style: theme.textTheme.bodySmall?.copyWith(
                  color: const Color(0xFF7F7C80),
                  fontWeight: FontWeight.w600,
                ),
              ),
              const Spacer(),
              Text(
                'Forgot Password?',
                style: theme.textTheme.bodySmall?.copyWith(
                  color: const Color(0xFF2E6FDB),
                  fontWeight: FontWeight.w700,
                ),
              ),
            ],
          ),
          const SizedBox(height: 28),
          ElevatedButton(
            onPressed: () {
              ScaffoldMessenger.of(context).showSnackBar(
                const SnackBar(
                  content: Text('Authentication flow will be added later.'),
                ),
              );
            },
            child: const Text('Login'),
          ),
          const SizedBox(height: 20),
          Row(
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              Text(
                'No account yet? ',
                style: theme.textTheme.bodySmall?.copyWith(
                  color: const Color(0xFFB0AEB2),
                  fontWeight: FontWeight.w600,
                ),
              ),
              Text(
                'Sign up',
                style: theme.textTheme.bodySmall?.copyWith(
                  color: const Color(0xFF2E6FDB),
                  fontWeight: FontWeight.w700,
                ),
              ),
            ],
          ),
          const SizedBox(height: 6),
        ],
      ),
    );
  }
}

class _LoginTextField extends StatelessWidget {
  const _LoginTextField({
    required this.hintText,
    required this.prefixIcon,
    this.obscureText = false,
    this.textInputAction,
  });

  final String hintText;
  final IconData prefixIcon;
  final bool obscureText;
  final TextInputAction? textInputAction;

  @override
  Widget build(BuildContext context) {
    return TextField(
      obscureText: obscureText,
      textInputAction: textInputAction,
      decoration: InputDecoration(
        hintText: hintText,
        prefixIcon: Icon(prefixIcon, size: 18, color: const Color(0xFF2E6FDB)),
        suffixIcon: obscureText
            ? const Icon(
                Icons.visibility_outlined,
                size: 18,
                color: Color(0xFFD3CED1),
              )
            : null,
      ),
    );
  }
}
